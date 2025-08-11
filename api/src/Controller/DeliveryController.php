<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\SalesList;
use App\Entity\Truck;
use App\Enum\DeliveryStatus;
use App\Repository\DeliveryRepository;
use App\Repository\SalesListRepository;
use App\Repository\TruckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[OA\Tag(name: "Delivery")]
#[Route('/api/deliveries')]
class DeliveryController extends AbstractController
{
    private function serializeDelivery(Delivery $d, bool $withQr = false): array
    {
        $out = [
            'id' => $d->getId(),
            'deliveryDate' => $d->getDeliveryDate()?->format('Y-m-d'),
            'deliveryAddress' => $d->getDeliveryAddress(),
            'deliveryNumber' => $d->getDeliveryNumber(),
            'deliveryStatus' => $d->getDeliveryStatus()?->value,
            'driverRemark' => $d->getDriverRemark(),
            'salesListId' => $d->getSalesList()?->getId(),
            'trucks' => array_map(fn(Truck $t) => [
                'id' => $t->getId(),
                'registrationNumber' => $t->getRegistrationNumber()
            ], $d->getTrucks()->toArray()),
        ];
        if ($withQr) {
            $out['qrCode'] = $d->getQrCode();
        }
        return $out;
    }

    /**
     * Returns a paginated list of deliveries.
     */
    #[OA\Get(
        path: '/api/deliveries',
        summary: 'List all deliveries (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20)),
        ],
        responses: [new OA\Response(response: 200, description: 'List of deliveries')]
    )]
    #[Route('', name: 'delivery_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, DeliveryRepository $repo): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $deliveries = $repo->findBy([], ['deliveryDate' => 'DESC'], $limit, $offset);

        $exposeQr = $this->isGranted('ROLE_DRIVER') || $this->isGranted('ROLE_ORDERPREPARER');
        $data = array_map(fn(Delivery $d) => $this->serializeDelivery($d, $exposeQr), $deliveries);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data,
        ]);
    }

    #[Route('/mine', methods: ['GET'])]
    #[IsGranted('ROLE_DRIVER')]
    public function mine(Request $req, DeliveryRepository $repo): JsonResponse {
        $status = $req->query->get('status'); // optional: in_preparation, in_progress
        $criteria = [];
        if ($status && DeliveryStatus::tryFrom($status)) {
            $criteria['deliveryStatus'] = DeliveryStatus::from($status);
        } else {
            $criteria['deliveryStatus'] = [DeliveryStatus::InPreparation, DeliveryStatus::InProgress];
        }
        $items = $repo->findBy($criteria, ['deliveryDate' => 'ASC'], 100);
        return $this->json(array_map(fn(Delivery $d) => [
            'id' => $d->getId(),
            'deliveryNumber' => $d->getDeliveryNumber(),
            'deliveryStatus' => $d->getDeliveryStatus()->value,
            'qrCode' => $d->getQrCode(),
            'deliveryAddress' => $d->getDeliveryAddress(),
            'deliveryDate' => $d->getDeliveryDate()?->format('Y-m-d'),
        ], $items));
    }

    /**
     * Creates a delivery for a sales list.
     */
    #[OA\Post(
        path: '/api/deliveries/salesLists/{id}/delivery',
        summary: 'Create a delivery for a sales list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['deliveryDate', 'deliveryAddress', 'deliveryNumber'],
                properties: [
                    new OA\Property(property: 'deliveryDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'deliveryAddress', type: 'string'),
                    new OA\Property(property: 'deliveryNumber', type: 'string')
                ]
            )
        ),
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 201, description: 'Delivery created'),
            new OA\Response(response: 409, description: 'Delivery already exists'),
        ]
    )]
    #[Route('/salesLists/{id}/delivery', name: 'salesList_delivery_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        SalesList $salesList,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        if ($salesList->getDelivery()) {
            return $this->json(['error' => 'Delivery already exists for this SalesList'], 409);
        }
        $data = json_decode($request->getContent(), true);
        $required = ['deliveryDate', 'deliveryAddress', 'deliveryNumber'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }

        $delivery = new Delivery();
        $delivery->setDeliveryDate(new \DateTime($data['deliveryDate']));
        $delivery->setDeliveryAddress($data['deliveryAddress']);
        $delivery->setDeliveryNumber($data['deliveryNumber']);
        $delivery->setDeliveryStatus(DeliveryStatus::InPreparation);
        $delivery->setQrCode('QR-' . Uuid::v4()->toRfc4122());
        $delivery->setSalesList($salesList);

        $em->persist($delivery);
        $em->flush();

        return $this->json([
            'message' => 'Delivery created successfully',
            'id' => $delivery->getId(),
        ], 201);
    }

    /**
     * Préparateur : scan pour démarrer la livraison (InPreparation -> InProgress).
     */
    #[OA\Post(
        path: '/api/deliveries/scan-prep',
        summary: 'Order preparer scan: mark delivery InProgress',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['qrCode'],
                properties: [new OA\Property(property: 'qrCode', type: 'string')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status updated'),
            new OA\Response(response: 404, description: 'QR not found'),
            new OA\Response(response: 409, description: 'Invalid transition'),
        ]
    )]
    #[Route('/scan-prep', name: 'delivery_scan_prep', methods: ['POST'])]
    #[IsGranted('ROLE_ORDERPREPARER')]
    public function scanPrep(Request $request, DeliveryRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $p = json_decode($request->getContent(), true) ?? [];
        $qr = $p['qrCode'] ?? null;
        if (!$qr) {
            return $this->json(['error' => 'qrCode required'], 400);
        }

        $d = $repo->findOneBy(['qrCode' => $qr]);
        if (!$d) {
            return $this->json(['error' => 'QR not found'], 404);
        }

        if ($d->getDeliveryStatus() !== DeliveryStatus::InPreparation) {
            return $this->json(['error' => 'Only InPreparation can be started'], 409);
        }

        $d->setDeliveryStatus(DeliveryStatus::InProgress);
        $em->flush();

        return $this->json([
            'message' => 'Delivery started',
            'deliveryNumber' => $d->getDeliveryNumber(),
            'deliveryStatus' => $d->getDeliveryStatus()->value,
        ]);
    }

    /**
     * Driver : scan pour livrer (→ Delivered).
     */
    #[OA\Post(
        path: '/api/deliveries/scan',
        summary: 'Driver scan: mark delivery Delivered',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['qrCode'],
                properties: [
                    new OA\Property(property: 'qrCode', type: 'string'),
                    new OA\Property(property: 'driverRemark', type: 'string', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status updated'),
            new OA\Response(response: 404, description: 'QR not found'),
            new OA\Response(response: 409, description: 'Invalid transition or already delivered'),
        ]
    )]
    #[Route('/scan', name: 'delivery_scan', methods: ['POST'])]
    #[IsGranted('ROLE_DRIVER')]
    public function scan(Request $request, DeliveryRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $p = json_decode($request->getContent(), true) ?? [];
        $qr = $p['qrCode'] ?? null;
        $remark = trim($p['driverRemark'] ?? '');
        if (!$qr) return $this->json(['error' => 'qrCode required'], 400);

        $d = $repo->findOneBy(['qrCode' => $qr]);
        if (!$d) return $this->json(['error' => 'QR not found'], 404);

        $s = $d->getDeliveryStatus();
        if ($s === DeliveryStatus::Delivered) {
            return $this->json(['status' => 'already_delivered', 'deliveryNumber' => $d->getDeliveryNumber()], 409);
        }
        if (!in_array($s, [DeliveryStatus::InPreparation, DeliveryStatus::InProgress], true)) {
            return $this->json(['error' => 'Invalid status transition'], 409);
        }

        $d->setDeliveryStatus(DeliveryStatus::Delivered);
        if ($remark !== '') $d->setDriverRemark($remark);
        if (method_exists($d, 'setDeliveredAt')) $d->setDeliveredAt(new \DateTimeImmutable());

        $em->flush();

        return $this->json([
            'message' => 'Delivery marked as delivered',
            'deliveryNumber' => $d->getDeliveryNumber(),
            'deliveryStatus' => $d->getDeliveryStatus()->value,
        ]);
    }

    

    /**
     * Returns the details of a delivery.
     */
    #[OA\Get(
        path: '/api/deliveries/{id}',
        summary: 'Get delivery details',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Delivery details'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    #[Route('/{id}', name: 'delivery_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(Delivery $delivery = null): JsonResponse
    {
        if (!$delivery) {
            return $this->json(['error' => 'Delivery not found'], 404);
        }
        $exposeQr = $this->isGranted('ROLE_DRIVER') || $this->isGranted('ROLE_ORDERPREPARER');
        return $this->json($this->serializeDelivery($delivery, $exposeQr));
    }

    /**
     * Updates a delivery.
     */
    #[OA\Put(
        path: '/api/deliveries/{id}',
        summary: 'Update a delivery',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'deliveryDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'deliveryAddress', type: 'string'),
                    new OA\Property(property: 'deliveryNumber', type: 'string'),
                    new OA\Property(property: 'deliveryStatus', type: 'string'),
                    new OA\Property(property: 'driverRemark', type: 'string'),
                ]
            )
        ),
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Delivery updated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    #[Route('/{id}', name: 'delivery_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, Delivery $delivery = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$delivery) {
            return $this->json(['error' => 'Delivery not found'], 404);
        }
        $data = json_decode($request->getContent(), true);

        if (isset($data['deliveryDate'])) {
            $delivery->setDeliveryDate(new \DateTime($data['deliveryDate']));
        }
        if (isset($data['deliveryAddress'])) {
            $delivery->setDeliveryAddress($data['deliveryAddress']);
        }
        if (isset($data['deliveryNumber'])) {
            $delivery->setDeliveryNumber($data['deliveryNumber']);
        }
        if (isset($data['deliveryStatus'])) {
            if (!DeliveryStatus::tryFrom($data['deliveryStatus'])) {
                return $this->json(['error' => 'Invalid deliveryStatus'], 400);
            }
            $delivery->setDeliveryStatus(DeliveryStatus::from($data['deliveryStatus']));
        }
        if (isset($data['driverRemark'])) {
            $delivery->setDriverRemark($data['driverRemark']);
        }

        $em->flush();

        return $this->json(['message' => 'Delivery updated successfully']);
    }

    /**
     * Deletes a delivery.
     */
    #[OA\Delete(
        path: '/api/deliveries/{id}',
        summary: 'Delete a delivery',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Delivery deleted'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    #[Route('/{id}', name: 'delivery_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Delivery $delivery = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$delivery) {
            return $this->json(['error' => 'Delivery not found'], 404);
        }
        $em->remove($delivery);
        $em->flush();

        return $this->json(['message' => 'Delivery deleted successfully']);
    }

    /**
     * Associates a truck to a delivery.
     */
    #[OA\Post(
        path: '/api/deliveries/{id}/trucks',
        summary: 'Associate a truck to a delivery',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['truckId'],
                properties: [new OA\Property(property: 'truckId', type: 'integer')]
            )
        ),
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 201, description: 'Truck associated'),
            new OA\Response(response: 404, description: 'Truck or delivery not found'),
            new OA\Response(response: 409, description: 'Truck already assigned to another delivery'),
        ]
    )]
    #[Route('/{id}/trucks', name: 'delivery_add_truck', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addTruck(
        Delivery $delivery,
        Request $request,
        TruckRepository $truckRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (empty($data['truckId'])) {
            return $this->json(['error' => 'Missing truckId'], 400);
        }
        $truck = $truckRepo->find($data['truckId']);
        if (!$truck) {
            return $this->json(['error' => 'Truck not found'], 404);
        }
        if ($truck->getDelivery() && $truck->getDelivery()->getId() !== $delivery->getId()) {
            return $this->json(['error' => 'Truck already assigned to another delivery'], 409);
        }
        $delivery->addTruck($truck);
        $em->flush();

        return $this->json(['message' => 'Truck associated to delivery'], 201);
    }

    /**
     * Removes a truck from a delivery.
     */
    #[OA\Delete(
        path: '/api/deliveries/{id}/trucks/{truckId}',
        summary: 'Remove a truck from a delivery',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck dissociated'),
            new OA\Response(response: 404, description: 'Association not found'),
        ]
    )]
    #[Route('/{id}/trucks/{truckId}', name: 'delivery_remove_truck', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function removeTruck(
        Delivery $delivery,
        int $truckId,
        TruckRepository $truckRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $truck = $truckRepo->find($truckId);
        if (!$truck || !$delivery->getTrucks()->contains($truck)) {
            return $this->json(['error' => 'Truck not associated with this delivery'], 404);
        }
        $delivery->removeTruck($truck);
        $em->flush();

        return $this->json(['message' => 'Truck dissociated from delivery']);
    }

    /**
     * Returns the delivery associated with a sales list.
     */
    #[OA\Get(
        path: '/api/deliveries/salesLists/{id}',
        summary: 'Get delivery by salesList ID',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Delivery details'),
            new OA\Response(response: 404, description: 'Delivery not found for this salesList'),
        ]
    )]
    #[Route('/salesLists/{id}', name: 'delivery_by_saleslist', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getDeliveryBySalesList(
        int $id,
        SalesListRepository $salesListRepo,
        DeliveryRepository $deliveryRepo
    ): JsonResponse {
        $salesList = $salesListRepo->find($id);
        if (!$salesList) {
            return $this->json(['error' => 'SalesList not found'], 404);
        }

        $delivery = $deliveryRepo->findOneBy(['salesList' => $salesList]);
        if (!$delivery) {
            return $this->json(['error' => 'No delivery found for this salesList'], 404);
        }

        $exposeQr = $this->isGranted('ROLE_DRIVER') || $this->isGranted('ROLE_ORDERPREPARER');
        return $this->json($this->serializeDelivery($delivery, $exposeQr));
    }
}

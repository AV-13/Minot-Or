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

#[OA\Tag(name: "Delivery")]
#[Route('/api/deliveries')]
class DeliveryController extends AbstractController
{
    /**
     * Returns a paginated list of deliveries.
     */
    #[OA\Get(
        path: '/api/deliveries',
        summary: 'List all deliveries (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of deliveries')
        ]
    )]
    #[Route('', name: 'delivery_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, DeliveryRepository $repo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $deliveries = $repo->findBy([], ['deliveryDate' => 'DESC'], $limit, $offset);

        $data = array_map(fn(Delivery $d) => [
            'id' => $d->getId(),
            'deliveryDate' => $d->getDeliveryDate()?->format('Y-m-d'),
            'deliveryAddress' => $d->getDeliveryAddress(),
            'deliveryNumber' => $d->getDeliveryNumber(),
            'deliveryStatus' => $d->getDeliveryStatus()?->value,
            'driverRemark' => $d->getDriverRemark(),
            'qrCode' => $d->getQrCode(),
            'salesListId' => $d->getSalesList()?->getId(),
            'trucks' => array_map(fn(Truck $t) => [
                'id' => $t->getId(),
                'registrationNumber' => $t->getRegistrationNumber()
            ], $d->getTrucks()->toArray())
        ], $deliveries);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Creates a delivery for a sales list.
     */
    #[OA\Post(
        path: '/salesLists/{id}/delivery',
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
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 201, description: 'Delivery created'),
            new OA\Response(response: 409, description: 'Delivery already exists')
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
        $delivery->setQrCode('QR-' . uniqid());
        $delivery->setSalesList($salesList);

        $em->persist($delivery);
        $em->flush();

        return $this->json([
            'message' => 'Delivery created successfully',
            'id' => $delivery->getId()
        ], 201);
    }

    /**
     * Returns the details of a delivery.
     */
    #[OA\Get(
        path: '/api/deliveries/{id}',
        summary: 'Get delivery details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Delivery details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'delivery_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(Delivery $delivery = null): JsonResponse
    {
        if (!$delivery) {
            return $this->json(['error' => 'Delivery not found'], 404);
        }
        return $this->json([
            'id' => $delivery->getId(),
            'deliveryDate' => $delivery->getDeliveryDate()?->format('Y-m-d'),
            'deliveryAddress' => $delivery->getDeliveryAddress(),
            'deliveryNumber' => $delivery->getDeliveryNumber(),
            'deliveryStatus' => $delivery->getDeliveryStatus()?->value,
            'driverRemark' => $delivery->getDriverRemark(),
            'qrCode' => $delivery->getQrCode(),
            'salesListId' => $delivery->getSalesList()?->getId(),
            'trucks' => array_map(fn(Truck $t) => [
                'id' => $t->getId(),
                'registrationNumber' => $t->getRegistrationNumber()
            ], $delivery->getTrucks()->toArray())
        ]);
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
                    new OA\Property(property: 'driverRemark', type: 'string')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Delivery updated'),
            new OA\Response(response: 404, description: 'Not found')
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
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Delivery deleted'),
            new OA\Response(response: 404, description: 'Not found')
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
                properties: [
                    new OA\Property(property: 'truckId', type: 'integer')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 201, description: 'Truck associated'),
            new OA\Response(response: 404, description: 'Truck or delivery not found'),
            new OA\Response(response: 409, description: 'Truck already assigned to another delivery')
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
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck dissociated'),
            new OA\Response(response: 404, description: 'Association not found')
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
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Delivery details'),
            new OA\Response(response: 404, description: 'Delivery not found for this salesList')
        ]
    )]

    #[Route('/salesLists/{id}', name: 'delivery_by_saleslist', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getDeliveryBySalesList(
        int $id,
        SalesListRepository $salesListRepo,
        DeliveryRepository $deliveryRepo
    ): JsonResponse
    {
        $salesList = $salesListRepo->find($id);
        if (!$salesList) {
            return $this->json(['error' => 'SalesList not found'], 404);
        }

        $delivery = $deliveryRepo->findOneBy(['salesList' => $salesList]);
        if (!$delivery) {
            return $this->json(['error' => 'No delivery found for this salesList'], 404);
        }

        return $this->json([
            'id' => $delivery->getId(),
            'deliveryDate' => $delivery->getDeliveryDate()?->format('Y-m-d'),
            'deliveryAddress' => $delivery->getDeliveryAddress(),
            'deliveryNumber' => $delivery->getDeliveryNumber(),
            'deliveryStatus' => $delivery->getDeliveryStatus()?->value,
            'driverRemark' => $delivery->getDriverRemark(),
            'qrCode' => $delivery->getQrCode(),
            'salesListId' => $delivery->getSalesList()?->getId()
        ]);
    }
}
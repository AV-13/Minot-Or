<?php

namespace App\Controller;

use App\Entity\Truck;
use App\Entity\Warehouse;
use App\Entity\User;
use App\Entity\Delivery;
use App\Entity\Clean;
use App\Enum\TruckCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Truck")]
#[Route('/api/trucks')]
class TruckController extends AbstractController
{
    /**
     * Creates a new truck.
     */
    #[OA\Post(
        path: '/api/trucks',
        summary: 'Create a truck',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['registrationNumber','truckType','isAvailable','deliveryCount','transportDistance','transportFee','idWarehouse'],
                properties: [
                    new OA\Property(property: 'registrationNumber', type: 'string', maxLength: 10),
                    new OA\Property(property: 'truckType', type: 'string'),
                    new OA\Property(property: 'isAvailable', type: 'boolean'),
                    new OA\Property(property: 'deliveryCount', type: 'integer'),
                    new OA\Property(property: 'transportDistance', type: 'number'),
                    new OA\Property(property: 'transportFee', type: 'number'),
                    new OA\Property(property: 'idWarehouse', type: 'integer'),
                    new OA\Property(property: 'idDriver', type: 'integer', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Truck created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', name: 'truck_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($em->getRepository(Truck::class)->findOneBy(['registrationNumber' => $data['registrationNumber'] ?? null])) {
            return $this->json(['error' => 'Registration number already exists'], 400);
        }

        $required = ['registrationNumber', 'truckType', 'isAvailable', 'deliveryCount', 'transportDistance', 'transportFee', 'idWarehouse'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Missing field: $field"], 400);
            }
        }

        if (!TruckCategory::tryFrom($data['truckType'])) {
            return $this->json(['error' => 'Invalid truck type'], 400);
        }

        $warehouse = $em->getRepository(Warehouse::class)->find($data['idWarehouse']);
        if (!$warehouse) {
            return $this->json(['error' => 'Warehouse not found'], 400);
        }

        $truck = new Truck();
        $truck->setRegistrationNumber($data['registrationNumber']);
        $truck->setTruckType(TruckCategory::from($data['truckType']));
        $truck->setIsAvailable((bool)$data['isAvailable']);
        $truck->setDeliveryCount((int)$data['deliveryCount']);
        $truck->setTransportDistance((float)$data['transportDistance']);
        $truck->setTransportFee((float)$data['transportFee']);
        $truck->setWarehouse($warehouse);

        if (!empty($data['idDriver'])) {
            $driver = $em->getRepository(User::class)->find($data['idDriver']);
            if (!$driver) {
                return $this->json(['error' => 'Driver not found'], 400);
            }
            $truck->setDriver($driver);
        }

        $em->persist($truck);
        $em->flush();

        return $this->json([
            'message' => 'Truck created successfully',
            'id' => $truck->getId()
        ], 201);
    }

    /**
     * Returns a paginated and filtered list of trucks.
     */
    #[OA\Get(
        path: '/api/trucks',
        summary: 'List trucks (paginated, filtered)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'warehouseId', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckType', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'isAvailable', in: 'query', schema: new OA\Schema(type: 'boolean'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated truck list')
        ]
    )]
    #[Route('', name: 'truck_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;
        $search = $request->query->get('search');

        $qb = $em->createQueryBuilder()
            ->select('t')
            ->from(Truck::class, 't');

        // Filtres classiques
        if ($request->query->get('warehouseId')) {
            $qb->andWhere('t.warehouse = :warehouseId')
               ->setParameter('warehouseId', $request->query->get('warehouseId'));
        }
        if ($request->query->get('truckType')) {
            $qb->andWhere('t.truckType = :truckType')
               ->setParameter('truckType', $request->query->get('truckType'));
        }
        if ($request->query->has('isAvailable')) {
            $qb->andWhere('t.isAvailable = :isAvailable')
               ->setParameter('isAvailable', filter_var($request->query->get('isAvailable'), FILTER_VALIDATE_BOOLEAN));
        }

        // Filtre recherche
        if ($search) {
            $qb->andWhere('t.registrationNumber LIKE :search OR t.truckType LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $trucks = $qb->getQuery()->getResult();

        // Compter le total filtré
        $countQb = clone $qb;
        $countQb->select('COUNT(t.id)');
        $total = $countQb->getQuery()->getSingleScalarResult();

        $data = array_map(fn(Truck $t) => [
            'id' => $t->getId(),
            'registrationNumber' => $t->getRegistrationNumber(),
            'truckType' => $t->getTruckType()?->value,
            'isAvailable' => $this->computeAvailability($t),
            'deliveryCount' => $t->getDeliveryCount(),
            'transportDistance' => $t->getTransportDistance(),
            'transportFee' => $t->getTransportFee(),
            'warehouse' => $t->getWarehouse()?->getId(),
            'driver' => $t->getDriver()?->getId()
        ], $trucks);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Returns the details of a truck.
     */
    #[OA\Get(
        path: '/api/trucks/{id}',
        summary: 'Get truck details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'truck_detail', methods: ['GET'])]
    public function detail(Truck $truck): JsonResponse
    {
        return $this->json([
            'id' => $truck->getId(),
            'registrationNumber' => $truck->getRegistrationNumber(),
            'truckType' => $truck->getTruckType()?->value,
            'isAvailable' => $this->computeAvailability($truck),
            'deliveryCount' => $truck->getDeliveryCount(),
            'transportDistance' => $truck->getTransportDistance(),
            'transportFee' => $truck->getTransportFee(),
            'warehouse' => $truck->getWarehouse()?->getId(),
            'driver' => $truck->getDriver()?->getId()
        ]);
    }

    /**
     * Updates a truck.
     */
    #[OA\Put(
        path: '/api/trucks/{id}',
        summary: 'Update a truck',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'registrationNumber', type: 'string', maxLength: 10),
                    new OA\Property(property: 'truckType', type: 'string'),
                    new OA\Property(property: 'isAvailable', type: 'boolean'),
                    new OA\Property(property: 'deliveryCount', type: 'integer'),
                    new OA\Property(property: 'transportDistance', type: 'number'),
                    new OA\Property(property: 'transportFee', type: 'number'),
                    new OA\Property(property: 'idWarehouse', type: 'integer'),
                    new OA\Property(property: 'idDriver', type: 'integer', nullable: true)
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}', name: 'truck_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Truck $truck, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['registrationNumber'])) {
            if ($em->getRepository(Truck::class)->findOneBy(['registrationNumber' => $data['registrationNumber']])) {
                return $this->json(['error' => 'Registration number already exists'], 400);
            }
            $truck->setRegistrationNumber($data['registrationNumber']);
        }
        if (isset($data['truckType'])) {
            if (!TruckCategory::tryFrom($data['truckType'])) {
                return $this->json(['error' => 'Invalid truck type'], 400);
            }
            $truck->setTruckType(TruckCategory::from($data['truckType']));
        }
        if (isset($data['isAvailable'])) {
            $truck->setIsAvailable((bool)$data['isAvailable']);
        }
        if (isset($data['deliveryCount'])) {
            $truck->setDeliveryCount((int)$data['deliveryCount']);
        }
        if (isset($data['transportDistance'])) {
            $truck->setTransportDistance((float)$data['transportDistance']);
        }
        if (isset($data['transportFee'])) {
            $truck->setTransportFee((float)$data['transportFee']);
        }
        if (isset($data['idWarehouse'])) {
            $warehouse = $em->getRepository(Warehouse::class)->find($data['idWarehouse']);
            if (!$warehouse) {
                return $this->json(['error' => 'Warehouse not found'], 400);
            }
            $truck->setWarehouse($warehouse);
        }
        if (array_key_exists('idDriver', $data)) {
            if ($data['idDriver']) {
                $driver = $em->getRepository(User::class)->find($data['idDriver']);
                if (!$driver) {
                    return $this->json(['error' => 'Driver not found'], 400);
                }
                $truck->setDriver($driver);
            } else {
                $truck->setDriver(null);
            }
        }

        $em->flush();

        return $this->json(['message' => 'Truck updated successfully']);
    }

    /**
     * Changes truck availability.
     */
    #[OA\Patch(
        path: '/api/trucks/{id}/status',
        summary: 'Change truck availability',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'isAvailable', type: 'boolean')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Status updated')
        ]
    )]
    #[Route('/{id}/status', name: 'truck_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function changeStatus(Truck $truck, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['isAvailable'])) {
            return $this->json(['error' => 'Missing isAvailable field'], 400);
        }
        $truck->setIsAvailable((bool)$data['isAvailable']);
        $em->flush();
        return $this->json(['message' => 'Availability updated']);
    }

    /**
     * Returns the cleaning history of a truck.
     */
    #[OA\Get(
        path: '/api/trucks/{id}/cleans',
        summary: 'Get truck cleaning history',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of cleanings')
        ]
    )]
    #[Route('/{id}/cleans', name: 'truck_cleans', methods: ['GET'])]
    public function cleans(Truck $truck): JsonResponse
    {
        $data = [];
        foreach ($truck->getCleans() as $clean) {
            $cleaning = $clean->getTruckCleaning();
            $data[] = [
                'cleaningId' => $cleaning?->getId(),
                'startDate' => $cleaning?->getCleaningStartDate()?->format('Y-m-d'),
                'endDate' => $cleaning?->getCleaningEndDate()?->format('Y-m-d'),
                'observations' => $cleaning?->getObservations()
            ];
        }
        return $this->json($data);
    }

    /**
     * Returns the delivery history of a truck.
     */
    #[OA\Get(
        path: '/api/trucks/{id}/deliveries',
        summary: 'Get truck delivery history',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of deliveries')
        ]
    )]
    #[Route('/{id}/deliveries', name: 'truck_deliveries', methods: ['GET'])]
    public function deliveries(Truck $truck, EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(Delivery::class);
        $deliveries = $repo->findBy(['truck' => $truck]);
        $data = [];
        foreach ($deliveries as $delivery) {
            $data[] = [
                'deliveryId' => $delivery->getId(),
                'deliveryDate' => $delivery->getDeliveryDate()?->format('Y-m-d'),
                'deliveryAddress' => $delivery->getDeliveryAddress(),
                'deliveryStatus' => $delivery->getDeliveryStatus()
            ];
        }
        return $this->json($data);
    }

    /**
     * Deletes a truck (if not used).
     */
    #[OA\Delete(
        path: '/api/trucks/{id}',
        summary: 'Delete a truck (if not used)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck deleted'),
            new OA\Response(response: 409, description: 'Truck cannot be deleted')
        ]
    )]
    #[Route('/{id}', name: 'truck_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Truck $truck, EntityManagerInterface $em): JsonResponse
    {
        if ($truck->getDelivery() || count($truck->getRestocks()) > 0) {
            return $this->json(['error' => 'Cannot delete this truck: it is used in a delivery or restock.'], 409);
        }
        $em->remove($truck);
        $em->flush();
        return $this->json(['message' => 'Truck deleted successfully']);
    }

    /**
     * Business logic: unavailable if in cleaning or delivery.
     */
    private function computeAvailability(Truck $truck): bool
    {
        if ($truck->getDelivery()) {
            return false;
        }
        foreach ($truck->getCleans() as $clean) {
            $end = $clean->getTruckCleaning()?->getCleaningEndDate();
            if ($end && $end > new \DateTime()) {
                return false;
            }
        }
        return $truck->isAvailable();
    }
}
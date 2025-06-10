<?php

namespace App\Controller;

use App\Entity\TruckCleaning;
use App\Entity\Truck;
use App\Entity\Clean;
use App\Repository\TruckCleaningRepository;
use App\Repository\TruckRepository;
use App\Repository\CleanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "TruckCleaning")]
#[Route('/api/cleanings')]
class TruckCleaningController extends AbstractController
{
    /**
     * Returns a paginated list of truck cleanings.
     */
    #[OA\Get(
        path: '/api/cleanings',
        summary: 'List all truck cleanings (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of truck cleanings')
        ]
    )]
    #[Route('', name: 'truck_cleaning_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, TruckCleaningRepository $repo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $cleanings = $repo->findBy([], ['cleaningStartDate' => 'DESC'], $limit, $offset);

        $data = array_map(fn(TruckCleaning $c) => [
            'id' => $c->getId(),
            'cleaningStartDate' => $c->getCleaningStartDate()?->format('Y-m-d'),
            'cleaningEndDate' => $c->getCleaningEndDate()?->format('Y-m-d'),
            'observations' => $c->getObservations(),
            'trucks' => array_map(
                fn(Clean $clean) => $clean->getTruck()?->getId(),
                $c->getCleans()->toArray()
            ),
        ], $cleanings);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Creates a new truck cleaning and associates a truck.
     */
    #[OA\Post(
        path: '/api/cleanings',
        summary: 'Create a new truck cleaning and associate a truck',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['cleaningStartDate','cleaningEndDate','observations','truckId'],
                properties: [
                    new OA\Property(property: 'cleaningStartDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'cleaningEndDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'observations', type: 'string'),
                    new OA\Property(property: 'truckId', type: 'integer')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Truck cleaning created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', name: 'truck_cleaning_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em, TruckRepository $truckRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $required = ['cleaningStartDate', 'cleaningEndDate', 'observations', 'truckId'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field: $field"], 400);
            }
        }

        $truck = $truckRepo->find($data['truckId']);
        if (!$truck) {
            return $this->json(['error' => 'Truck not found'], 404);
        }

        $cleaning = new TruckCleaning();
        $cleaning->setCleaningStartDate(new \DateTime($data['cleaningStartDate']));
        $cleaning->setCleaningEndDate(new \DateTime($data['cleaningEndDate']));
        $cleaning->setObservations($data['observations']);

        $em->persist($cleaning);
        $em->flush();

        $clean = new Clean();
        $clean->setTruck($truck);
        $clean->setTruckCleaning($cleaning);

        $em->persist($clean);
        $em->flush();

        return $this->json([
            'message' => 'Truck cleaning created and truck associated',
            'id' => $cleaning->getId()
        ], 201);
    }

    /**
     * Returns the details of a truck cleaning.
     */
    #[OA\Get(
        path: '/api/cleanings/{id}',
        summary: 'Get a specific truck cleaning',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck cleaning details'),
            new OA\Response(response: 404, description: 'Truck cleaning not found')
        ]
    )]
    #[Route('/{id}', name: 'truck_cleaning_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(TruckCleaning $cleaning = null): JsonResponse
    {
        if (!$cleaning) {
            return $this->json(['error' => 'Truck cleaning not found'], 404);
        }

        return $this->json([
            'id' => $cleaning->getId(),
            'cleaningStartDate' => $cleaning->getCleaningStartDate()?->format('Y-m-d'),
            'cleaningEndDate' => $cleaning->getCleaningEndDate()?->format('Y-m-d'),
            'observations' => $cleaning->getObservations(),
            'trucks' => array_map(
                fn(Clean $clean) => $clean->getTruck()?->getId(),
                $cleaning->getCleans()->toArray()
            ),
        ]);
    }

    /**
     * Updates a truck cleaning.
     */
    #[OA\Put(
        path: '/api/cleanings/{id}',
        summary: 'Update a truck cleaning',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'cleaningStartDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'cleaningEndDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'observations', type: 'string')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck cleaning updated'),
            new OA\Response(response: 404, description: 'Truck cleaning not found')
        ]
    )]
    #[Route('/{id}', name: 'truck_cleaning_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, TruckCleaning $cleaning = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$cleaning) {
            return $this->json(['error' => 'Truck cleaning not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['cleaningStartDate'])) {
            $cleaning->setCleaningStartDate(new \DateTime($data['cleaningStartDate']));
        }
        if (isset($data['cleaningEndDate'])) {
            $cleaning->setCleaningEndDate(new \DateTime($data['cleaningEndDate']));
        }
        if (isset($data['observations'])) {
            $cleaning->setObservations($data['observations']);
        }

        $em->flush();

        return $this->json(['message' => 'Truck cleaning updated successfully']);
    }

    /**
     * Deletes a truck cleaning.
     */
    #[OA\Delete(
        path: '/api/cleanings/{id}',
        summary: 'Delete a truck cleaning',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck cleaning deleted'),
            new OA\Response(response: 404, description: 'Truck cleaning not found')
        ]
    )]
    #[Route('/{id}', name: 'truck_cleaning_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(TruckCleaning $cleaning = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$cleaning) {
            return $this->json(['error' => 'Truck cleaning not found'], 404);
        }

        $em->remove($cleaning);
        $em->flush();

        return $this->json(['message' => 'Truck cleaning deleted successfully']);
    }

    /**
     * Associates a truck to a cleaning cycle.
     */
    #[OA\Post(
        path: '/api/cleanings/{id}/trucks',
        summary: 'Associate a truck to a cleaning cycle',
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
            new OA\Response(response: 404, description: 'Truck or cleaning not found'),
            new OA\Response(response: 409, description: 'Truck already associated')
        ]
    )]
    #[Route('/{id}/trucks', name: 'truck_cleaning_add_truck', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addTruck(
        TruckCleaning $cleaning,
        Request $request,
        TruckRepository $truckRepo,
        CleanRepository $cleanRepo,
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

        $existing = $cleanRepo->findOneBy(['truckCleaning' => $cleaning, 'truck' => $truck]);
        if ($existing) {
            return $this->json(['error' => 'Truck already associated'], 409);
        }

        $clean = new Clean();
        $clean->setTruck($truck);
        $clean->setTruckCleaning($cleaning);

        $em->persist($clean);
        $em->flush();

        return $this->json(['message' => 'Truck associated to cleaning'], 201);
    }

    /**
     * Removes a truck from a cleaning cycle.
     */
    #[OA\Delete(
        path: '/api/cleanings/{id}/trucks/{truckId}',
        summary: 'Remove a truck from a cleaning cycle',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Truck dissociated'),
            new OA\Response(response: 404, description: 'Association not found')
        ]
    )]
    #[Route('/{id}/trucks/{truckId}', name: 'truck_cleaning_remove_truck', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function removeTruck(
        TruckCleaning $cleaning,
        int $truckId,
        CleanRepository $cleanRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $clean = $cleanRepo->findOneBy(['truckCleaning' => $cleaning, 'truck' => $truckId]);
        if (!$clean) {
            return $this->json(['error' => 'Truck not associated with this cleaning'], 404);
        }

        $em->remove($clean);
        $em->flush();

        return $this->json(['message' => 'Truck dissociated from cleaning']);
    }
}
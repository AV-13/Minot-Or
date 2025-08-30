<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Warehouse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Warehouse")]
#[Route('/api/warehouses')]
final class WarehouseController extends AbstractController
{
    /**
     * Creates a new warehouse.
     */
    #[OA\Post(
        path: '/api/warehouses',
        summary: 'Create a new warehouse',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['warehouseAddress', 'storageCapacity'],
                properties: [
                    new OA\Property(property: 'warehouseAddress', type: 'string', maxLength: 10),
                    new OA\Property(property: 'storageCapacity', type: 'integer', minimum: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Warehouse created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', name: 'warehouse_create', methods: ['POST'])]
    #[Security("is_granted('ROLE_SALES') or is_granted('ROLE_PROCUREMENT') or is_granted('ROLE_ADMIN')", message: "Access denied.")]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['warehouseAddress']) || empty($data['storageCapacity'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }
        if (mb_strlen($data['warehouseAddress']) > 10) {
            return $this->json(['error' => 'Address too long (max 10 characters)'], 400);
        }
        if (!is_numeric($data['storageCapacity']) || $data['storageCapacity'] <= 0) {
            return $this->json(['error' => 'Invalid storage capacity'], 400);
        }

        $warehouse = new Warehouse();
        $warehouse
            ->setWarehouseAddress($data['warehouseAddress'])
            ->setStorageCapacity((int)$data['storageCapacity']);

        $em->persist($warehouse);
        $em->flush();

        return $this->json([
            'message' => 'Warehouse created successfully',
            'id' => $warehouse->getId()
        ], 201);
    }

    /**
     * Returns a paginated list of warehouses.
     */
    #[OA\Get(
        path: '/api/warehouses',
        summary: 'List warehouses (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated warehouse list')
        ]
    )]
    #[Route('', name: 'warehouse_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;
        $search = $request->query->get('search', '');

        $repo = $em->getRepository(Warehouse::class);

        $qb = $repo->createQueryBuilder('w');
        if ($search) {
            $qb->where('LOWER(w.warehouseAddress) LIKE :search')
               ->setParameter('search', '%' . strtolower($search) . '%');
        }
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $warehouses = $qb->getQuery()->getResult();

        $countQb = $repo->createQueryBuilder('w');
        if ($search) {
            $countQb->where('LOWER(w.warehouseAddress) LIKE :search')
                    ->setParameter('search', '%' . strtolower($search) . '%');
        }
        $total = (int)$countQb->select('COUNT(w.id)')->getQuery()->getSingleScalarResult();

        $data = array_map(fn(Warehouse $w) => [
            'id' => $w->getId(),
            'warehouseAddress' => $w->getWarehouseAddress(),
            'storageCapacity' => $w->getStorageCapacity()
        ], $warehouses);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Returns the details of a warehouse.
     */
    #[OA\Get(
        path: '/api/warehouses/{id}',
        summary: 'Get warehouse details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Warehouse details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'warehouse_detail', methods: ['GET'])]
    public function detail(Warehouse $warehouse): JsonResponse
    {
        return $this->json([
            'id' => $warehouse->getId(),
            'warehouseAddress' => $warehouse->getWarehouseAddress(),
            'storageCapacity' => $warehouse->getStorageCapacity()
        ]);
    }

    /**
     * Updates a warehouse.
     */
    #[OA\Put(
        path: '/api/warehouses/{id}',
        summary: 'Update a warehouse',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'warehouseAddress', type: 'string', maxLength: 10),
                    new OA\Property(property: 'storageCapacity', type: 'integer', minimum: 1)
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Warehouse updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}', name: 'warehouse_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Warehouse $warehouse, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['warehouseAddress'])) {
            if (mb_strlen($data['warehouseAddress']) > 10) {
                return $this->json(['error' => 'Address too long (max 10 characters)'], 400);
            }
            $warehouse->setWarehouseAddress($data['warehouseAddress']);
        }
        if (isset($data['storageCapacity'])) {
            if (!is_numeric($data['storageCapacity']) || $data['storageCapacity'] <= 0) {
                return $this->json(['error' => 'Invalid storage capacity'], 400);
            }
            $warehouse->setStorageCapacity((int)$data['storageCapacity']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Warehouse updated successfully'
        ]);
    }

    /**
     * Deletes a warehouse (only if empty).
     */
    #[OA\Delete(
        path: '/api/warehouses/{id}',
        summary: 'Delete a warehouse (only if empty)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Warehouse deleted'),
            new OA\Response(response: 409, description: 'Warehouse not empty')
        ]
    )]
    #[Route('/{id}', name: 'warehouse_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Warehouse $warehouse, EntityManagerInterface $em): JsonResponse
    {
        if (count($warehouse->getProducts() ?? []) > 0) {
            return $this->json([
                'error' => 'Cannot delete warehouse: products are still stored here.'
            ], 409);
        }

        $em->remove($warehouse);
        $em->flush();

        return $this->json([
            'message' => 'Warehouse deleted successfully'
        ]);
    }

    /**
     * Returns all product stock in a warehouse.
     */
    #[OA\Get(
        path: '/api/warehouses/{id}/stock',
        summary: 'Get all product stock in a warehouse',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Warehouse stock')
        ]
    )]
    #[Route('/{id}/stock', name: 'warehouse_stock', methods: ['GET'])]
    public function stock(Warehouse $warehouse, EntityManagerInterface $em): JsonResponse
    {
        $products = $em->getRepository(Product::class)->findBy(['warehouse' => $warehouse]);

        $data = array_map(fn(Product $p) => [
            'productId' => $p->getId(),
            'productName' => $p->getProductName(),
            'stockQuantity' => $p->getStockQuantity()
        ], $products);

        return $this->json($data);
    }
}
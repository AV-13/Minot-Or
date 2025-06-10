<?php

namespace App\Controller;

use App\Entity\Restock;
use App\Entity\Supplier;
use App\Entity\Truck;
use App\Entity\Product;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Restock")]
#[Route('/api/restocks')]
class RestockController extends AbstractController
{
    /**
     * Returns a paginated and filterable list of restocks.
     */
    #[OA\Get(
        path: '/api/restocks',
        summary: 'List all restocks (paginated, filterable)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20)),
            new OA\Parameter(name: 'orderStatus', in: 'query', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of restocks')
        ]
    )]
    #[Route('', name: 'restock_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $orderStatus = $request->query->get('orderStatus');

        $criteria = [];
        if ($orderStatus && OrderStatus::tryFrom($orderStatus)) {
            $criteria['orderStatus'] = OrderStatus::from($orderStatus);
        }

        $repo = $em->getRepository(Restock::class);
        $total = $repo->count($criteria);
        $restocks = $repo->findBy($criteria, [], $limit, ($page - 1) * $limit);

        $data = array_map(fn(Restock $r) => [
            'supplierId' => $r->getSupplier()?->getId(),
            'supplierName' => $r->getSupplier()?->getSupplierName(),
            'truckId' => $r->getTruck()?->getId(),
            'productId' => $r->getProduct()?->getId(),
            'productName' => $r->getProduct()?->getProductName(),
            'quantity' => $r->getSupplierProductQuantity(),
            'orderNumber' => $r->getOrderNumber(),
            'orderDate' => $r->getOrderDate()?->format('Y-m-d'),
            'orderStatus' => $r->getOrderStatus()?->value
        ], $restocks);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Creates a new restock order.
     */
    #[OA\Post(
        path: '/api/restocks',
        summary: 'Create a new restock',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['supplierId', 'truckId', 'productId', 'supplierProductQuantity', 'orderNumber', 'orderDate', 'orderStatus'],
                properties: [
                    new OA\Property(property: 'supplierId', type: 'integer'),
                    new OA\Property(property: 'truckId', type: 'integer'),
                    new OA\Property(property: 'productId', type: 'integer'),
                    new OA\Property(property: 'supplierProductQuantity', type: 'integer'),
                    new OA\Property(property: 'orderNumber', type: 'string'),
                    new OA\Property(property: 'orderDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'orderStatus', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Restock created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', name: 'restock_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $required = ['supplierId', 'truckId', 'productId', 'supplierProductQuantity', 'orderNumber', 'orderDate', 'orderStatus'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Missing field: $field"], 400);
            }
        }

        $supplier = $em->getRepository(Supplier::class)->find($data['supplierId']);
        $truck = $em->getRepository(Truck::class)->find($data['truckId']);
        $product = $em->getRepository(Product::class)->find($data['productId']);

        if (!$supplier || !$truck || !$product) {
            return $this->json(['error' => 'Supplier, truck or product not found'], 400);
        }
        if (!OrderStatus::tryFrom($data['orderStatus'])) {
            return $this->json(['error' => 'Invalid orderStatus'], 400);
        }

        $existing = $em->getRepository(Restock::class)->findOneBy([
            'supplier' => $supplier,
            'truck' => $truck,
            'product' => $product
        ]);
        if ($existing) {
            return $this->json(['error' => 'Restock already exists for this supplier/truck/product'], 409);
        }

        $restock = new Restock();
        $restock->setSupplier($supplier);
        $restock->setTruck($truck);
        $restock->setProduct($product);
        $restock->setSupplierProductQuantity((int)$data['supplierProductQuantity']);
        $restock->setOrderNumber($data['orderNumber']);
        $restock->setOrderDate(new \DateTime($data['orderDate']));
        $restock->setOrderStatus(OrderStatus::from($data['orderStatus']));

        $em->persist($restock);
        $em->flush();

        return $this->json(['message' => 'Restock created', 'supplierId' => $supplier->getId(), 'truckId' => $truck->getId(), 'productId' => $product->getId()], 201);
    }

    /**
     * Returns the details of a restock (composite key).
     */
    #[OA\Get(
        path: '/api/restocks/{supplierId}/{truckId}/{productId}',
        summary: 'Get restock details (composite key)',
        parameters: [
            new OA\Parameter(name: 'supplierId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Restock details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{supplierId}/{truckId}/{productId}', name: 'restock_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail($supplierId, $truckId, $productId, EntityManagerInterface $em): JsonResponse
    {
        $restock = $em->getRepository(Restock::class)->findOneBy([
            'supplier' => $supplierId,
            'truck' => $truckId,
            'product' => $productId
        ]);
        if (!$restock) {
            return $this->json(['error' => 'Restock not found'], 404);
        }

        return $this->json([
            'supplierId' => $restock->getSupplier()?->getId(),
            'supplierName' => $restock->getSupplier()?->getSupplierName(),
            'truckId' => $restock->getTruck()?->getId(),
            'productId' => $restock->getProduct()?->getId(),
            'productName' => $restock->getProduct()?->getProductName(),
            'quantity' => $restock->getSupplierProductQuantity(),
            'orderNumber' => $restock->getOrderNumber(),
            'orderDate' => $restock->getOrderDate()?->format('Y-m-d'),
            'orderStatus' => $restock->getOrderStatus()?->value
        ]);
    }

    /**
     * Updates a restock (composite key).
     */
    #[OA\Put(
        path: '/api/restocks/{supplierId}/{truckId}/{productId}',
        summary: 'Update a restock (composite key)',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'supplierProductQuantity', type: 'integer'),
                    new OA\Property(property: 'orderNumber', type: 'string'),
                    new OA\Property(property: 'orderDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'orderStatus', type: 'string')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'supplierId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Restock updated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{supplierId}/{truckId}/{productId}', name: 'restock_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update($supplierId, $truckId, $productId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $restock = $em->getRepository(Restock::class)->findOneBy([
            'supplier' => $supplierId,
            'truck' => $truckId,
            'product' => $productId
        ]);
        if (!$restock) {
            return $this->json(['error' => 'Restock not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['supplierProductQuantity'])) {
            $restock->setSupplierProductQuantity((int)$data['supplierProductQuantity']);
        }
        if (isset($data['orderNumber'])) {
            $restock->setOrderNumber($data['orderNumber']);
        }
        if (isset($data['orderDate'])) {
            $restock->setOrderDate(new \DateTime($data['orderDate']));
        }
        if (isset($data['orderStatus']) && OrderStatus::tryFrom($data['orderStatus'])) {
            $restock->setOrderStatus(OrderStatus::from($data['orderStatus']));
        }

        $em->flush();

        return $this->json(['message' => 'Restock updated']);
    }

    /**
     * Changes the status of a restock.
     */
    #[OA\Patch(
        path: '/api/restocks/{supplierId}/{truckId}/{productId}/status',
        summary: 'Change restock status',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['orderStatus'],
                properties: [
                    new OA\Property(property: 'orderStatus', type: 'string')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'supplierId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Status updated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{supplierId}/{truckId}/{productId}/status', name: 'restock_patch_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function patchStatus($supplierId, $truckId, $productId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $restock = $em->getRepository(Restock::class)->findOneBy([
            'supplier' => $supplierId,
            'truck' => $truckId,
            'product' => $productId
        ]);
        if (!$restock) {
            return $this->json(['error' => 'Restock not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (!isset($data['orderStatus'])) {
            return $this->json(['error' => 'Missing orderStatus'], 400);
        }
        if (!OrderStatus::tryFrom($data['orderStatus'])) {
            return $this->json(['error' => 'Invalid orderStatus'], 400);
        }
        $restock->setOrderStatus(OrderStatus::from($data['orderStatus']));
        $em->flush();
        return $this->json(['message' => 'Order status updated']);
    }

    /**
     * Deletes a restock (composite key).
     */
    #[OA\Delete(
        path: '/api/restocks/{supplierId}/{truckId}/{productId}',
        summary: 'Delete a restock (composite key)',
        parameters: [
            new OA\Parameter(name: 'supplierId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'truckId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Restock deleted'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{supplierId}/{truckId}/{productId}', name: 'restock_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete($supplierId, $truckId, $productId, EntityManagerInterface $em): JsonResponse
    {
        $restock = $em->getRepository(Restock::class)->findOneBy([
            'supplier' => $supplierId,
            'truck' => $truckId,
            'product' => $productId
        ]);
        if (!$restock) {
            return $this->json(['error' => 'Restock not found'], 404);
        }
        $em->remove($restock);
        $em->flush();
        return $this->json(['message' => 'Restock deleted']);
    }
}
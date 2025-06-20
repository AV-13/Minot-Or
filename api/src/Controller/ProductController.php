<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Warehouse;
use App\Enum\ProductCategory;
use App\Repository\ProductRepository;
use App\Service\SecurityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Product")]
#[Route('/api/products')]
class ProductController extends AbstractController
{
    /**
     * Creates a new product.
     */
    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['productName','quantity','netPrice','grossPrice','unitWeight','category','warehouseId'],
                properties: [
                    new OA\Property(property: 'productName', type: 'string', maxLength: 50),
                    new OA\Property(property: 'quantity', type: 'number'),
                    new OA\Property(property: 'netPrice', type: 'number'),
                    new OA\Property(property: 'grossPrice', type: 'number'),
                    new OA\Property(property: 'unitWeight', type: 'number'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'category', description: 'Product category', type: 'string'),
                    new OA\Property(property: 'warehouseId', type: 'integer')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created'),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 403, description: 'Access denied')
        ]
    )]
    #[Route('', name: 'product_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SecurityHelper $securityHelper
    ): JsonResponse {
        if (!$securityHelper->hasRole('Sales')) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $requiredFields = ['productName', 'quantity', 'netPrice', 'grossPrice', 'unitWeight', 'category', 'warehouseId'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field: $field"], 400);
            }
        }

        $warehouse = $em->getRepository(Warehouse::class)->find($data['warehouseId']);
        if (!$warehouse) {
            return $this->json(['error' => 'Warehouse not found'], 404);
        }

        $category = ProductCategory::tryFrom($data['category']);
        if (!$category) {
            return $this->json(['error' => 'Invalid category'], 400);
        }

        $product = new Product();
        $product
            ->setProductName($data['productName'])
            ->setQuantity($data['quantity'])
            ->setNetPrice($data['netPrice'])
            ->setGrossPrice($data['grossPrice'])
            ->setUnitWeight($data['unitWeight'])
            ->setCategory($category)
            ->setStockQuantity(0)
            ->setWarehouse($warehouse);

        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }

        $em->persist($product);
        $em->flush();

        return $this->json(['message' => 'Product created successfully', 'id' => $product->getId()], 201);
    }

    /**
     * Returns a paginated list of products, with optional filtering.
     */
    #[OA\Get(
        path: '/api/products',
        summary: 'List products (with filtering and pagination)',
        parameters: [
            new OA\Parameter(name: 'category', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'warehouse', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated product list')
        ]
    )]
    #[Route('', name: 'product_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $categoryParam = $request->query->get('category');
        $warehouseParam = $request->query->get('warehouse');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 10));
        $offset = ($page - 1) * $limit;
        $search = $request->query->get('search', '');

        $qb = $em->getRepository(Product::class)->createQueryBuilder('p');

        if ($categoryParam) {
            $category = ProductCategory::tryFrom($categoryParam);
            if (!$category) {
                return $this->json(['error' => 'Invalid category'], 400);
            }
            $qb->andWhere('p.category = :category')
               ->setParameter('category', $category);
        }
        if ($warehouseParam) {
            if (!is_numeric($warehouseParam)) {
                return $this->json(['error' => 'Invalid warehouse ID'], 400);
            }
            $qb->andWhere('p.warehouse = :warehouse')
               ->setParameter('warehouse', (int)$warehouseParam);
        }
        if ($search) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'LOWER(p.productName) LIKE :search',
                    'LOWER(p.description) LIKE :search'
                )
            )->setParameter('search', '%' . strtolower($search) . '%');
        }

        $total = (clone $qb)->select('COUNT(p.id)')->getQuery()->getSingleScalarResult();

        $products = $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $items = array_map(function (Product $product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getProductName(),
                'quantity' => $product->getQuantity(),
                'stockQuantity' => $product->getStockQuantity(),
                'netPrice' => $product->getNetPrice(),
                'grossPrice' => $product->getGrossPrice(),
                'unitWeight' => $product->getUnitWeight(),
                'category' => $product->getCategory()->value,
                'warehouseId' => $product->getWarehouse()->getId(),
                'description' => $product->getDescription(),
            ];
        }, $products);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $items
        ]);
    }

    /**
     * Returns the details of a product.
     */
    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get product details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product details'),
            new OA\Response(response: 404, description: 'Product not found')
        ]
    )]
    #[Route('/{id}', name: 'product_detail', methods: ['GET'])]
    public function detail(Product $product): JsonResponse
    {
        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getProductName(),
            'quantity' => $product->getQuantity(),
            'stockQuantity' => $product->getStockQuantity(),
            'netPrice' => $product->getNetPrice(),
            'grossPrice' => $product->getGrossPrice(),
            'unitWeight' => $product->getUnitWeight(),
            'category' => $product->getCategory()->value,
            'warehouseId' => $product->getWarehouse()->getId()
        ]);
    }

    /**
     * Updates a product (except stock).
     */
    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update a product (except stock)',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'productName', type: 'string', maxLength: 50),
                    new OA\Property(property: 'quantity', type: 'number'),
                    new OA\Property(property: 'netPrice', type: 'number'),
                    new OA\Property(property: 'grossPrice', type: 'number'),
                    new OA\Property(property: 'unitWeight', type: 'number'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'category', type: 'string'),
                    new OA\Property(property: 'warehouseId', type: 'integer')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(Request $request, Product $product, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['productName'])) $product->setProductName($data['productName']);
        if (isset($data['quantity'])) $product->setQuantity($data['quantity']);
        if (isset($data['netPrice'])) $product->setNetPrice($data['netPrice']);
        if (isset($data['grossPrice'])) $product->setGrossPrice($data['grossPrice']);
        if (isset($data['unitWeight'])) $product->setUnitWeight($data['unitWeight']);
        if (isset($data['description'])) $product->setDescription($data['description']);
        if (isset($data['category'])) {
            $category = ProductCategory::tryFrom($data['category']);
            if (!$category) {
                return $this->json(['error' => 'Invalid category'], 400);
            }
            $product->setCategory($category);
        }
        if (isset($data['warehouseId'])) {
            $warehouse = $em->getRepository(Warehouse::class)->find($data['warehouseId']);
            if ($warehouse) {
                $product->setWarehouse($warehouse);
            }
        }
        $em->flush();
        return $this->json(['message' => 'Product updated successfully']);
    }

    /**
     * Deletes a product (if not used in an order or restock).
     */
    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Delete a product (if not used in an order or restock)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product deleted'),
            new OA\Response(response: 409, description: 'Product cannot be deleted')
        ]
    )]
    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $em): JsonResponse
    {
        if (count($product->getContains() ?? []) > 0) {
            return $this->json([
                'error' => 'Cannot delete this product: it is used in a client order.'
            ], 409);
        }
        if (count($product->getRestocks() ?? []) > 0) {
            return $this->json([
                'error' => 'Cannot delete this product: it is used in a restock order.'
            ], 409);
        }

        $em->remove($product);
        $em->flush();
        return $this->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Updates product stock.
     */
    #[OA\Patch(
        path: '/api/products/{id}/stock',
        summary: 'Update product stock',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['stockQuantity'],
                properties: [
                    new OA\Property(property: 'stockQuantity', type: 'integer')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Stock updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}/stock', name: 'product_update_stock', methods: ['PATCH'])]
    public function updateStock(Product $product, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['stockQuantity'])) {
            return $this->json(['error' => 'Missing stockQuantity'], 400);
        }
        $product->setStockQuantity((int)$data['stockQuantity']);
        $em->flush();
        return $this->json(['message' => 'Stock updated', 'stockQuantity' => $product->getStockQuantity()]);
    }
}
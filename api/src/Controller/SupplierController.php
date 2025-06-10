<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Entity\Product;
use App\Entity\ProductSupplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Supplier")]
#[Route('/api/suppliers')]
class SupplierController extends AbstractController
{
    /**
     * Creates a new supplier.
     */
    #[OA\Post(
        path: '/api/suppliers',
        summary: 'Create a new supplier',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['supplierName', 'supplierAddress'],
                properties: [
                    new OA\Property(property: 'supplierName', type: 'string', maxLength: 50),
                    new OA\Property(property: 'supplierAddress', type: 'string', maxLength: 50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Supplier created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', name: 'supplier_create', methods: ['POST'])]
    #[IsGranted('ROLE_SALES')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['supplierName']) || empty($data['supplierAddress'])) {
            return $this->json(['error' => 'Missing supplierName or supplierAddress'], 400);
        }
        if (mb_strlen($data['supplierName']) > 50 || mb_strlen($data['supplierAddress']) > 50) {
            return $this->json(['error' => 'supplierName or supplierAddress too long'], 400);
        }

        $supplier = new Supplier();
        $supplier->setSupplierName($data['supplierName']);
        $supplier->setSupplierAddress($data['supplierAddress']);

        $em->persist($supplier);
        $em->flush();

        return $this->json(['message' => 'Supplier created', 'id' => $supplier->getId()], 201);
    }

    /**
     * Returns a paginated list of suppliers.
     */
    #[OA\Get(
        path: '/api/suppliers',
        summary: 'List suppliers (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated supplier list')
        ]
    )]
    #[Route('', name: 'supplier_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $repo = $em->getRepository(Supplier::class);
        $total = $repo->count([]);
        $suppliers = $repo->findBy([], [], $limit, $offset);

        $data = array_map(fn(Supplier $s) => [
            'id' => $s->getId(),
            'supplierName' => $s->getSupplierName(),
            'supplierAddress' => $s->getSupplierAddress()
        ], $suppliers);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Returns the details of a supplier.
     */
    #[OA\Get(
        path: '/api/suppliers/{id}',
        summary: 'Get supplier details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Supplier details'),
            new OA\Response(response: 404, description: 'Supplier not found')
        ]
    )]
    #[Route('/{id}', name: 'supplier_detail', methods: ['GET'])]
    public function detail(Supplier $supplier): JsonResponse
    {
        return $this->json([
            'id' => $supplier->getId(),
            'supplierName' => $supplier->getSupplierName(),
            'supplierAddress' => $supplier->getSupplierAddress()
        ]);
    }

    /**
     * Updates a supplier.
     */
    #[OA\Put(
        path: '/api/suppliers/{id}',
        summary: 'Update a supplier',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'supplierName', type: 'string', maxLength: 50),
                    new OA\Property(property: 'supplierAddress', type: 'string', maxLength: 50)
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Supplier updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('/{id}', name: 'supplier_update', methods: ['PUT'])]
    #[IsGranted('ROLE_SALES')]
    public function update(Request $request, Supplier $supplier, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['supplierName'])) {
            if (mb_strlen($data['supplierName']) > 50) {
                return $this->json(['error' => 'supplierName too long'], 400);
            }
            $supplier->setSupplierName($data['supplierName']);
        }
        if (isset($data['supplierAddress'])) {
            if (mb_strlen($data['supplierAddress']) > 50) {
                return $this->json(['error' => 'supplierAddress too long'], 400);
            }
            $supplier->setSupplierAddress($data['supplierAddress']);
        }

        $em->flush();
        return $this->json(['message' => 'Supplier updated']);
    }

    /**
     * Deletes a supplier.
     */
    #[OA\Delete(
        path: '/api/suppliers/{id}',
        summary: 'Delete a supplier',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Supplier deleted')
        ]
    )]
    #[Route('/{id}', name: 'supplier_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Supplier $supplier, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($supplier);
        $em->flush();
        return $this->json(['message' => 'Supplier deleted']);
    }

    /**
     * Returns all products associated with a supplier.
     */
    #[OA\Get(
        path: '/api/suppliers/{id}/products',
        summary: 'List all products associated with a supplier',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of products')
        ]
    )]
    #[Route('/{id}/products', name: 'supplier_products', methods: ['GET'])]
    public function listProducts(Supplier $supplier, EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(ProductSupplier::class);
        $assocs = $repo->findBy(['supplier' => $supplier]);
        $products = array_map(function (ProductSupplier $ps) {
            $p = $ps->getProduct();
            return [
                'id' => $p->getId(),
                'productName' => $p->getProductName(),
                'category' => $p->getCategory()?->value,
                'stockQuantity' => $p->getStockQuantity()
            ];
        }, $assocs);

        return $this->json($products);
    }

    /**
     * Associates a product to a supplier.
     */
    #[OA\Post(
        path: '/api/suppliers/{id}/products',
        summary: 'Associate a product to a supplier',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['productId'],
                properties: [
                    new OA\Property(property: 'productId', type: 'integer')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product associated'),
            new OA\Response(response: 409, description: 'Already associated')
        ]
    )]
    #[Route('/{id}/products', name: 'supplier_add_product', methods: ['POST'])]
    #[IsGranted('ROLE_SALES')]
    public function addProduct(
        Supplier $supplier,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (empty($data['productId'])) {
            return $this->json(['error' => 'Missing productId'], 400);
        }
        $product = $em->getRepository(Product::class)->find($data['productId']);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $repo = $em->getRepository(ProductSupplier::class);
        $already = $repo->findOneBy(['product' => $product, 'supplier' => $supplier]);
        if ($already) {
            return $this->json(['error' => 'This product is already associated with this supplier'], 409);
        }

        $assoc = new ProductSupplier();
        $assoc->setSupplier($supplier);
        $assoc->setProduct($product);

        $em->persist($assoc);
        $em->flush();

        return $this->json(['message' => 'Product associated with supplier']);
    }

    /**
     * Dissociates a product from a supplier.
     */
    #[OA\Delete(
        path: '/api/suppliers/{id}/products/{productId}',
        summary: 'Dissociate a product from a supplier',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product dissociated'),
            new OA\Response(response: 404, description: 'Association not found')
        ]
    )]
    #[Route('/{id}/products/{productId}', name: 'supplier_remove_product', methods: ['DELETE'])]
    #[IsGranted('ROLE_SALES')]
    public function removeProduct(
        Supplier $supplier,
        int $productId,
        EntityManagerInterface $em
    ): JsonResponse {
        $repo = $em->getRepository(ProductSupplier::class);
        $assoc = $repo->findOneBy(['product' => $productId, 'supplier' => $supplier]);
        if (!$assoc) {
            return $this->json(['error' => 'Association not found'], 404);
        }

        $em->remove($assoc);
        $em->flush();

        return $this->json(['message' => 'Product dissociated from supplier']);
    }
}
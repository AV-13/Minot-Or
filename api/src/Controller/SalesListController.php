<?php

namespace App\Controller;

use App\Entity\SalesList;
use App\Entity\Product;
use App\Entity\Contains;
use App\Entity\User;
use App\Enum\SalesStatus;
use App\Repository\SalesListRepository;
use App\Repository\ProductRepository;
use App\Repository\ContainsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "SalesList")]
#[Route('/api/salesLists')]
class SalesListController extends AbstractController
{
    /**
     * Returns a paginated list of sales lists (quotes/orders).
     */
    #[OA\Get(
        path: '/api/salesLists',
        summary: 'List all sales lists (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of sales lists')
        ]
    )]
    #[Route('', name: 'salesList_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, SalesListRepository $repo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $salesLists = $repo->findBy([], null, $limit, $offset);

        $data = array_map(fn(SalesList $s) => [
            'id' => $s->getId(),
            'status' => $s->getStatus()?->value,
            'productsPrice' => $s->getProductsPrice(),
            'globalDiscount' => $s->getGlobalDiscount(),
            'issueDate' => $s->getIssueDate()?->format('Y-m-d H:i:s'),
            'expirationDate' => $s->getExpirationDate()?->format('Y-m-d H:i:s'),
            'orderDate' => $s->getOrderDate()?->format('Y-m-d H:i:s'),
        ], $salesLists);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Creates a new sales list (quote/order).
     */
    #[OA\Post(
        path: '/api/salesLists',
        summary: 'Create a new sales list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status', 'productsPrice', 'globalDiscount', 'issueDate', 'expirationDate', 'orderDate'],
                properties: [
                    new OA\Property(property: 'status', type: 'string'),
                    new OA\Property(property: 'productsPrice', type: 'number'),
                    new OA\Property(property: 'globalDiscount', type: 'integer'),
                    new OA\Property(property: 'issueDate', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'expirationDate', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'orderDate', type: 'string', format: 'date-time')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sales list created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[Route('', name: 'salesList_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['status'], $data['productsPrice'], $data['globalDiscount'], $data['issueDate'], $data['expirationDate'], $data['orderDate'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $salesList = new SalesList();
        $salesList->setStatus(SalesStatus::from($data['status']));
        $salesList->setProductsPrice((float)$data['productsPrice']);
        $salesList->setGlobalDiscount((int)$data['globalDiscount']);
        $salesList->setIssueDate(new \DateTime($data['issueDate']));
        $salesList->setExpirationDate(new \DateTime($data['expirationDate']));
        $salesList->setOrderDate(new \DateTime($data['orderDate']));

        $em->persist($salesList);
        $em->flush();

        return $this->json([
            'message' => 'Sales list created successfully',
            'id' => $salesList->getId()
        ], 201);
    }

    /**
     * Returns the details of a sales list.
     */
    #[OA\Get(
        path: '/api/salesLists/{id}',
        summary: 'Get a specific sales list',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sales list details'),
            new OA\Response(response: 404, description: 'Sales list not found')
        ]
    )]
    #[Route('/{id}', name: 'salesList_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(SalesList $salesList = null): JsonResponse
    {
        if (!$salesList) {
            return $this->json(['error' => 'Sales list not found'], 404);
        }

        return $this->json([
            'id' => $salesList->getId(),
            'status' => $salesList->getStatus()?->value,
            'productsPrice' => $salesList->getProductsPrice(),
            'globalDiscount' => $salesList->getGlobalDiscount(),
            'issueDate' => $salesList->getIssueDate()?->format('Y-m-d H:i:s'),
            'expirationDate' => $salesList->getExpirationDate()?->format('Y-m-d H:i:s'),
            'orderDate' => $salesList->getOrderDate()?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Updates a sales list.
     */
    #[OA\Put(
        path: '/api/salesLists/{id}',
        summary: 'Update a sales list',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string'),
                    new OA\Property(property: 'productsPrice', type: 'number'),
                    new OA\Property(property: 'globalDiscount', type: 'integer'),
                    new OA\Property(property: 'issueDate', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'expirationDate', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'orderDate', type: 'string', format: 'date-time')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sales list updated'),
            new OA\Response(response: 404, description: 'Sales list not found')
        ]
    )]
    #[Route('/{id}', name: 'salesList_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, SalesList $salesList = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$salesList) {
            return $this->json(['error' => 'Sales list not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['status'])) {
            $salesList->setStatus(SalesStatus::from($data['status']));
        }
        if (isset($data['productsPrice'])) {
            $salesList->setProductsPrice((float)$data['productsPrice']);
        }
        if (isset($data['globalDiscount'])) {
            $salesList->setGlobalDiscount((int)$data['globalDiscount']);
        }
        if (isset($data['issueDate'])) {
            $salesList->setIssueDate(new \DateTime($data['issueDate']));
        }
        if (isset($data['expirationDate'])) {
            $salesList->setExpirationDate(new \DateTime($data['expirationDate']));
        }
        if (isset($data['orderDate'])) {
            $salesList->setOrderDate(new \DateTime($data['orderDate']));
        }

        $em->flush();

        return $this->json(['message' => 'Sales list updated successfully']);
    }

    /**
     * Deletes a sales list.
     */
    #[OA\Delete(
        path: '/api/salesLists/{id}',
        summary: 'Delete a sales list',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sales list deleted'),
            new OA\Response(response: 404, description: 'Sales list not found')
        ]
    )]
    #[Route('/{id}', name: 'salesList_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(SalesList $salesList = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$salesList) {
            return $this->json(['error' => 'Sales list not found'], 404);
        }

        $em->remove($salesList);
        $em->flush();

        return $this->json(['message' => 'Sales list deleted successfully']);
    }

    /**
     * Returns all products for a sales list.
     */
    #[OA\Get(
        path: '/api/salesLists/{id}/products',
        summary: 'Get all products for a sales list',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of products for the sales list')
        ]
    )]
    #[Route('/{id}/products', name: 'salesList_products', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getProducts(SalesList $salesList, ContainsRepository $containsRepo): JsonResponse
    {
        $contains = $containsRepo->findBy(['salesList' => $salesList]);
        $products = array_map(fn(Contains $c) => [
            'productId' => $c->getProduct()?->getId(),
            'productName' => $c->getProduct()?->getProductName(),
            'productQuantity' => $c->getProductQuantity(),
            'productGrossPrice' => $c->getProduct()?->getGrossPrice(),
            'productNetPrice' => $c->getProduct()?->getNetPrice(),
            'productDiscount' => $c->getProductDiscount()
        ], $contains);

        return $this->json($products);
    }

    /**
     * Adds a product to a sales list.
     */
    #[OA\Post(
        path: '/api/salesLists/{id}/products',
        summary: 'Add a product to a sales list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['productId', 'productQuantity', 'productDiscount'],
                properties: [
                    new OA\Property(property: 'productId', type: 'integer'),
                    new OA\Property(property: 'productQuantity', type: 'integer'),
                    new OA\Property(property: 'productDiscount', type: 'integer')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 201, description: 'Product added to sales list'),
            new OA\Response(response: 404, description: 'Product or sales list not found'),
            new OA\Response(response: 409, description: 'Product already in sales list')
        ]
    )]
    #[Route('/{id}/products', name: 'salesList_add_product', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addProduct(
        SalesList $salesList,
        Request $request,
        ProductRepository $productRepo,
        ContainsRepository $containsRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['productId'], $data['productQuantity'], $data['productDiscount'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $product = $productRepo->find($data['productId']);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $existing = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $product]);
        if ($existing) {
            return $this->json(['error' => 'Product already in sales list'], 409);
        }

        $contain = new Contains();
        $contain->setSalesList($salesList);
        $contain->setProduct($product);
        $contain->setProductQuantity((int)$data['productQuantity']);
        $contain->setProductDiscount((int)$data['productDiscount']);

        $em->persist($contain);
        $em->flush();

        return $this->json(['message' => 'Product added to sales list'], 201);
    }

    /**
     * Removes a product from a sales list.
     */
    #[OA\Delete(
        path: '/api/salesLists/{id}/products/{productId}',
        summary: 'Remove a product from a sales list',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product removed from sales list'),
            new OA\Response(response: 404, description: 'Product or sales list not found')
        ]
    )]
    #[Route('/{id}/products/{productId}', name: 'salesList_remove_product', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function removeProduct(
        SalesList $salesList,
        int $productId,
        ContainsRepository $containsRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $contain = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $productId]);
        if (!$contain) {
            return $this->json(['error' => 'Product not found in sales list'], 404);
        }

        $em->remove($contain);
        $em->flush();

        return $this->json(['message' => 'Product removed from sales list']);
    }
    #[Route('/user/{userId}', name: 'salesList_user_orders', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUserOrders(int $userId, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $qb = $em->createQueryBuilder();
        $qb->select('s, d, c, p')
            ->from(SalesList::class, 's')
            ->leftJoin('s.delivery', 'd')
            ->leftJoin('s.contains', 'c')
            ->leftJoin('c.product', 'p')
            ->join('s.evaluates', 'e')
            ->where('e.reviewer = :user')
            ->setParameter('user', $user);

        $salesLists = $qb->getQuery()->getResult();

        $data = array_map(function (SalesList $salesList) {
            $delivery = $salesList->getDelivery();
            $contains = $salesList->getContains();
            $products = array_map(fn($contain) => $contain->getProduct()?->getProductName(), $contains->toArray());

            return [
                'id' => $salesList->getId(),
                'status' => $salesList->getStatus()?->value,
                'products' => $products,
                'delivery' => $delivery ? [
                    'id' => $delivery->getId(),
                    'address' => $delivery->getDeliveryAddress(),
                    'date' => $salesList->getDelivery()?->getDeliveryDate()?->format('d-m-Y H:i'),
                ] : null,
            ];
        }, $salesLists);

        return $this->json($data);
    }
}
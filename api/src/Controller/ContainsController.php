<?php

namespace App\Controller;

use App\Entity\Contains;
use App\Entity\Product;
use App\Entity\SalesList;
use App\Repository\ContainsRepository;
use App\Repository\ProductRepository;
use App\Repository\SalesListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Contains")]
#[Route('/api/contains')]
class ContainsController extends AbstractController
{
    /**
     * Returns a paginated list of contains entries.
     */
    #[OA\Get(
        path: '/api/contains',
        summary: 'List all contains entries (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of contains entries')
        ]
    )]
    #[Route('', name: 'contains_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, ContainsRepository $repo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $contains = $repo->findBy([], null, $limit, $offset);

        $data = array_map(fn(Contains $c) => [
            'salesListId' => $c->getSalesList()?->getId(),
            'productId' => $c->getProduct()?->getId(),
            'productName' => $c->getProduct()?->getProductName(),
            'productQuantity' => $c->getProductQuantity(),
            'productDiscount' => $c->getProductDiscount()
        ], $contains);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Adds a product to a sales list.
     */
    #[OA\Post(
        path: '/api/contains',
        summary: 'Add a product to a sales list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['salesListId', 'productId', 'productQuantity', 'productDiscount'],
                properties: [
                    new OA\Property(property: 'salesListId', type: 'integer'),
                    new OA\Property(property: 'productId', type: 'integer'),
                    new OA\Property(property: 'productQuantity', type: 'integer'),
                    new OA\Property(property: 'productDiscount', type: 'integer')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product added to sales list'),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 404, description: 'Product or sales list not found'),
            new OA\Response(response: 409, description: 'Product already in sales list')
        ]
    )]
    #[Route('', name: 'contains_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        SalesListRepository $salesListRepo,
        ProductRepository $productRepo,
        ContainsRepository $containsRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['salesListId'], $data['productId'], $data['productQuantity'], $data['productDiscount'])) {
            return $this->json(['error' => 'Champs requis manquants'], 400);
        }

        $salesList = $salesListRepo->find($data['salesListId']);
        if (!$salesList) {
            return $this->json(['error' => 'Liste de vente non trouvée'], 404);
        }

        $product = $productRepo->find($data['productId']);
        if (!$product) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }

        $existing = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $product]);
        if ($existing) {
            return $this->json(['error' => 'Ce produit est déjà dans la liste'], 409);
        }

        $contain = new Contains();
        $contain->setSalesList($salesList);
        $contain->setProduct($product);
        $contain->setProductQuantity((int)$data['productQuantity']);
        $contain->setProductDiscount((int)$data['productDiscount']);

        $em->persist($contain);
        $em->flush();

        return $this->json([
            'message' => 'Produit ajouté à la liste de vente',
            'salesListId' => $salesList->getId(),
            'productId' => $product->getId(),
            'productQuantity' => $contain->getProductQuantity(),
            'productDiscount' => $contain->getProductDiscount()
        ], 201);
    }

    /**
     * Updates the quantity and discount of a product in a sales list.
     */
    #[OA\Put(
        path: '/api/contains/{salesListId}/{productId}',
        summary: 'Update a contains entry',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'productQuantity', type: 'integer'),
                    new OA\Property(property: 'productDiscount', type: 'integer')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'salesListId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Contains entry updated'),
            new OA\Response(response: 404, description: 'Contains entry not found')
        ]
    )]
    #[Route('/{salesListId}/{productId}', name: 'contains_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(
        int $salesListId,
        int $productId,
        Request $request,
        ContainsRepository $containsRepo,
        SalesListRepository $salesListRepo,
        ProductRepository $productRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $salesList = $salesListRepo->find($salesListId);
        $product = $productRepo->find($productId);

        if (!$salesList || !$product) {
            return $this->json(['error' => 'Liste de vente ou produit non trouvé'], 404);
        }

        $contain = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $product]);
        if (!$contain) {
            return $this->json(['error' => 'Produit non trouvé dans cette liste'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['productQuantity'])) {
            $contain->setProductQuantity((int)$data['productQuantity']);
        }

        if (isset($data['productDiscount'])) {
            $contain->setProductDiscount((int)$data['productDiscount']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Entrée mise à jour avec succès',
            'salesListId' => $salesListId,
            'productId' => $productId,
            'productQuantity' => $contain->getProductQuantity(),
            'productDiscount' => $contain->getProductDiscount()
        ]);
    }

    /**
     * Deletes a contains entry.
     */
    #[OA\Delete(
        path: '/api/contains/{salesListId}/{productId}',
        summary: 'Delete a contains entry',
        parameters: [
            new OA\Parameter(name: 'salesListId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Contains entry deleted'),
            new OA\Response(response: 404, description: 'Contains entry not found')
        ]
    )]
    #[Route('/{salesListId}/{productId}', name: 'contains_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function delete(
        int $salesListId,
        int $productId,
        SalesListRepository $salesListRepo,
        ProductRepository $productRepo,
        ContainsRepository $containsRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $salesList = $salesListRepo->find($salesListId);
        $product = $productRepo->find($productId);

        if (!$salesList || !$product) {
            return $this->json(['error' => 'Liste de vente ou produit non trouvé'], 404);
        }

        $contain = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $product]);
        if (!$contain) {
            return $this->json(['error' => 'Produit non trouvé dans cette liste'], 404);
        }

        $em->remove($contain);
        $em->flush();

        return $this->json(['message' => 'Produit retiré de la liste de vente']);
    }
}
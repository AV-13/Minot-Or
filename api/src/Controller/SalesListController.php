<?php

namespace App\Controller;

use App\Entity\SalesList;
use App\Entity\Product;
use App\Entity\Contains;
use App\Enum\SalesStatus;
use App\Repository\ProductRepository;
use App\Repository\ContainsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/salesLists')]
final class SalesListController extends AbstractController
{
    // GET all saleslists
    #[Route('', name: 'salesList_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $salesLists = $em->getRepository(SalesList::class)->findAll();

        $data = array_map(fn(SalesList $s) => [
            'id' => $s->getId(),
            'status' => $s->getStatus()->value,
            'productsPrice' => $s->getProductsPrice(),
            'globalDiscount' => $s->getGlobalDiscount(),
            'issueDate' => $s->getIssueDate()->format('Y-m-d H:i:s'),
            'expirationDate' => $s->getExpirationDate()->format('Y-m-d H:i:s'),
            'orderDate' => $s->getOrderDate()->format('Y-m-d H:i:s'),
        ], $salesLists);

        return $this->json($data);
    }

    // GET all products for a saleslist
    #[Route('/{id}/products', name: 'salesList_products', methods: ['GET'])]
    public function getProducts(SalesList $salesList, ContainsRepository $containsRepo): JsonResponse
    {
        $contains = $containsRepo->findBy(['salesList' => $salesList]);
        $products = array_map(fn(Contains $c) => [
            'productId' => $c->getProduct()->getId(),
            'productName' => $c->getProduct()->getProductName(),
            'quantity' => $c->getProductQuantity(),
            'discount' => $c->getProductDiscount(),
        ], $contains);

        return $this->json($products);
    }

    // CREATE a saleslist
    #[Route('', name: 'salesList_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Champs obligatoires
        $required = ['status', 'productsPrice', 'globalDiscount', 'issueDate', 'expirationDate', 'orderDate'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }

        // Enum check
        if (!SalesStatus::tryFrom($data['status'])) {
            return $this->json(['error' => 'Invalid status'], 400);
        }

        try {
            $salesList = new SalesList();
            $salesList->setStatus(SalesStatus::from($data['status']));
            $salesList->setProductsPrice((float) $data['productsPrice']);
            $salesList->setGlobalDiscount((int) $data['globalDiscount']);
            $salesList->setIssueDate(new \DateTime($data['issueDate']));
            $salesList->setExpirationDate(new \DateTime($data['expirationDate']));
            $salesList->setOrderDate(new \DateTime($data['orderDate']));

            $em->persist($salesList);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid data: '.$e->getMessage()], 400);
        }

        return $this->json([
            'id' => $salesList->getId(),
            'status' => $salesList->getStatus()->value,
            'productsPrice' => $salesList->getProductsPrice(),
            'globalDiscount' => $salesList->getGlobalDiscount(),
            'issueDate' => $salesList->getIssueDate()->format('Y-m-d H:i:s'),
            'expirationDate' => $salesList->getExpirationDate()->format('Y-m-d H:i:s'),
            'orderDate' => $salesList->getOrderDate()->format('Y-m-d H:i:s'),
        ], 201);
    }

    // UPDATE a saleslist
    #[Route('/{id}', name: 'salesList_update', methods: ['PUT'])]
    public function update(Request $request, SalesList $salesList, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            if (isset($data['status'])) {
                if (!SalesStatus::tryFrom($data['status'])) {
                    return $this->json(['error' => 'Invalid status'], 400);
                }
                $salesList->setStatus(SalesStatus::from($data['status']));
            }
            if (isset($data['productsPrice'])) $salesList->setProductsPrice((float)$data['productsPrice']);
            if (isset($data['globalDiscount'])) $salesList->setGlobalDiscount((int)$data['globalDiscount']);
            if (isset($data['issueDate'])) $salesList->setIssueDate(new \DateTime($data['issueDate']));
            if (isset($data['expirationDate'])) $salesList->setExpirationDate(new \DateTime($data['expirationDate']));
            if (isset($data['orderDate'])) $salesList->setOrderDate(new \DateTime($data['orderDate']));
            $em->flush();
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid data: '.$e->getMessage()], 400);
        }

        return $this->json([
            'id' => $salesList->getId(),
            'status' => $salesList->getStatus()->value,
            'productsPrice' => $salesList->getProductsPrice(),
            'globalDiscount' => $salesList->getGlobalDiscount(),
            'issueDate' => $salesList->getIssueDate()->format('Y-m-d H:i:s'),
            'expirationDate' => $salesList->getExpirationDate()->format('Y-m-d H:i:s'),
            'orderDate' => $salesList->getOrderDate()->format('Y-m-d H:i:s'),
        ]);
    }

    // DELETE a saleslist
    #[Route('/{id}', name: 'salesList_delete', methods: ['DELETE'])]
    public function delete(SalesList $salesList, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($salesList);
        $em->flush();
        return $this->json(null, 204);
    }

    // ADD a product to a saleslist
    #[Route('/{id}/products', name: 'salesList_add_product', methods: ['POST'])]
    public function addProduct(
        SalesList $salesList,
        Request $request,
        ProductRepository $productRepo,
        EntityManagerInterface $em,
        ContainsRepository $containsRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['productId'])) {
            return $this->json(['error' => 'Missing productId'], 400);
        }
        $product = $productRepo->find($data['productId']);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        // Vérifier si déjà présent (clé primaire composite !)
        $existing = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $product]);
        if ($existing) {
            return $this->json(['error' => 'Product already in salesList'], 409);
        }

        $contains = new Contains();
        $contains->setSalesList($salesList);
        $contains->setProduct($product);
        $contains->setProductQuantity($data['quantity'] ?? 1);
        $contains->setProductDiscount($data['discount'] ?? 0);

        $em->persist($contains);
        $em->flush();

        return $this->json([
            'message' => 'Product added to salesList',
            'salesListId' => $salesList->getId(),
            'productId' => $product->getId()
        ], 201);
    }

    // REMOVE a product from a saleslist
    #[Route('/{id}/products/{productId}', name: 'salesList_remove_product', methods: ['DELETE'])]
    public function removeProduct(
        SalesList $salesList,
        int $productId,
        ContainsRepository $containsRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $contains = $containsRepo->findOneBy(['salesList' => $salesList, 'product' => $productId]);
        if (!$contains) {
            return $this->json(['error' => 'Product not in salesList'], 404);
        }
        $em->remove($contains);
        $em->flush();
        return $this->json(['message' => 'Product removed from salesList'], 200);
    }

    // PATCH status (seulement le statut)
    #[Route('/{id}/status', name: 'salesList_patch_status', methods: ['PATCH'])]
    public function patchStatus(Request $request, SalesList $salesList, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['status'])) {
            return $this->json(['error' => 'Missing status'], 400);
        }
        if (!SalesStatus::tryFrom($data['status'])) {
            return $this->json(['error' => 'Invalid status'], 400);
        }
        $salesList->setStatus(SalesStatus::from($data['status']));
        $em->flush();
        return $this->json([
            'message' => 'Status updated',
            'status' => $salesList->getStatus()->value
        ], 200);
    }
}

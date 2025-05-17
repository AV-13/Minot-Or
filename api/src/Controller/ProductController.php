<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Warehouse;
use App\Enum\ProductCategory;
use App\Repository\ProductRepository;
use App\Service\SecurityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(
        Request $request,
        EntityManagerInterface $em,
        SecurityHelper $securityHelper
    ): JsonResponse {

        if (!$securityHelper->hasRole('Sales')) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        // Validation simple
        $requiredFields = ['productName', 'quantity', 'netPrice', 'grossPrice', 'unitWeight', 'category', 'warehouseId'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "Missing field: $field"], 400);
            }
        }

        // Vérifie que l'entrepôt existe
        $warehouse = $em->getRepository(Warehouse::class)->find($data['warehouseId']);
        if (!$warehouse) {
            return new JsonResponse(['error' => 'Warehouse not found'], 404);
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
            ->setCategory($category) // Attention : enum ou string ?
            ->setStockQuantity(0) // Valeur initiale
            ->setWarehouse($warehouse);

        $em->persist($product);
        $em->flush();

        return new JsonResponse(['message' => 'Product created successfully'], 201);
    }


    #[Route('/api/products', name: 'get_products', methods: ['GET'])]
    public function getFilteredProducts(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $categoryParam = $request->query->get('category');
        $warehouseParam = $request->query->get('warehouse');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, (int) $request->query->get('limit', 10));
        $offset = ($page - 1) * $limit;

        $criteria = [];

        if ($categoryParam) {
            $category = ProductCategory::tryFrom($categoryParam);
            if (!$category) {
                return $this->json(['error' => 'Invalid category'], 400);
            }
            $criteria['category'] = $category;
        }

        if ($warehouseParam) {
            if (!is_numeric($warehouseParam)) {
                return $this->json(['error' => 'Invalid warehouse ID'], 400);
            }
            $criteria['warehouse'] = (int) $warehouseParam;
        }

        $total = $productRepository->count($criteria);
        $products = $productRepository->findBy($criteria, [], $limit, $offset);

        $items = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getProductName(),
                'quantity' => $product->getQuantity(),
                'stockQuantity' => $product->getStockQuantity(),
                'netPrice' => $product->getNetPrice(),
                'grossPrice' => $product->getGrossPrice(),
                'unitWeight' => $product->getUnitWeight(),
                'category' => $product->getCategory()->value,
                'warehouseId' => $product->getWarehouse()->getId()
            ];
        }, $products);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $items
        ]);
    }
    #[Route('/api/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
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
    #[Route('/api/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(Request $request, Product $product, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['productName'])) $product->setProductName($data['productName']);
        if (isset($data['quantity'])) $product->setQuantity($data['quantity']);
        if (isset($data['netPrice'])) $product->setNetPrice($data['netPrice']);
        if (isset($data['grossPrice'])) $product->setGrossPrice($data['grossPrice']);
        if (isset($data['unitWeight'])) $product->setUnitWeight($data['unitWeight']);
        if (isset($data['category'])) {
            $category = ProductCategory::tryFrom($data['category']);
            if (!$category) {
                return $this->json(['error' => 'Invalid category'], 400);
            }
            $product->setCategory($category);
        }
        if (isset($data['warehouseId'])) {
            // Optionnel : vérifier que l'entrepôt existe
            // ...
        }
        $em->flush();
        return $this->json(['message' => 'Product updated successfully']);
    }
    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(Product $product, EntityManagerInterface $em): JsonResponse
    {
        // Vérifie présence dans Restock
        if (count($product->getRestocks() ?? []) > 0) {
            return $this->json([
                'error' => 'Impossible de supprimer ce produit : il est utilisé dans une commande de réapprovisionnement.'
            ], 409);
        }
        // (ajoute d'autres vérifs selon ton modèle, ex: Contains, etc.)

        $em->remove($product);
        $em->flush();
        return $this->json(['message' => 'Product deleted successfully']);
    }

    #[Route('/api/products/{id}/stock', name: 'update_product_stock', methods: ['PATCH'])]
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

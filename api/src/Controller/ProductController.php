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

}

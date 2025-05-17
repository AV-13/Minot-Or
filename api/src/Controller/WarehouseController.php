<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Warehouse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/warehouses')]
final class WarehouseController extends AbstractController
{
    #[Route('', name: 'create_warehouse', methods: ['POST'])]
    public function createWarehouse(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['warehouseAddress']) || empty($data['storageCapacity'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }

        $warehouse = new Warehouse();
        $warehouse
            ->setWarehouseAddress($data['warehouseAddress'])
            ->setStorageCapacity($data['storageCapacity']);

        $em->persist($warehouse);
        $em->flush();

        return $this->json([
            'message' => 'Warehouse created successfully',
            'id' => $warehouse->getId()
        ], 201);
    }
    // Lister tous les entrepôts
    #[Route('', name: 'list_warehouses', methods: ['GET'])]
    public function listWarehouses(EntityManagerInterface $em): JsonResponse
    {
        $warehouses = $em->getRepository(Warehouse::class)->findAll();

        $data = array_map(fn(Warehouse $w) => [
            'id' => $w->getId(),
            'warehouseAddress' => $w->getWarehouseAddress(),
            'storageCapacity' => $w->getStorageCapacity()
        ], $warehouses);

        return $this->json($data);
    }

// Voir le détail d’un entrepôt
    #[Route('/{id}', name: 'get_warehouse', methods: ['GET'])]
    public function getWarehouse(Warehouse $warehouse): JsonResponse
    {
        return $this->json([
            'id' => $warehouse->getId(),
            'warehouseAddress' => $warehouse->getWarehouseAddress(),
            'storageCapacity' => $warehouse->getStorageCapacity()
        ]);
    }

// Modifier un entrepôt
    #[Route('/{id}', name: 'update_warehouse', methods: ['PUT'])]
    public function updateWarehouse(
        Warehouse $warehouse,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!empty($data['warehouseAddress'])) {
            $warehouse->setWarehouseAddress($data['warehouseAddress']);
        }
        if (!empty($data['storageCapacity'])) {
            $warehouse->setStorageCapacity($data['storageCapacity']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Warehouse updated successfully'
        ]);
    }

// Supprimer un entrepôt
    #[Route('/{id}', name: 'delete_warehouse', methods: ['DELETE'])]
    public function deleteWarehouse(
        Warehouse $warehouse,
        EntityManagerInterface $em
    ): JsonResponse {
        // Vérifier s'il y a des produits liés à cet entrepôt
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
    // Voir le stock de tous les produits dans un entrepôt
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

    // Ajouter du stock pour un produit
    #[Route('/{id}/stock/add', name: 'warehouse_stock_add', methods: ['POST'])]
    public function addStock(
        Warehouse $warehouse,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['productId']) || empty($data['quantity'])) {
            return $this->json(['error' => 'Missing productId or quantity'], 400);
        }
        $product = $em->getRepository(Product::class)->find($data['productId']);
        if (!$product || $product->getWarehouse()->getId() !== $warehouse->getId()) {
            return $this->json(['error' => 'Product not found in this warehouse'], 404);
        }

        $product->setStockQuantity($product->getStockQuantity() + $data['quantity']);
        $em->flush();

        return $this->json([
            'message' => 'Stock increased',
            'productId' => $product->getId(),
            'newQuantity' => $product->getStockQuantity()
        ]);
    }

    // Retirer du stock pour un produit
    #[Route('/{id}/stock/remove', name: 'warehouse_stock_remove', methods: ['POST'])]
    public function removeStock(
        Warehouse $warehouse,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['productId']) || empty($data['quantity'])) {
            return $this->json(['error' => 'Missing productId or quantity'], 400);
        }
        $product = $em->getRepository(Product::class)->find($data['productId']);
        if (!$product || $product->getWarehouse()->getId() !== $warehouse->getId()) {
            return $this->json(['error' => 'Product not found in this warehouse'], 404);
        }
        if ($product->getStockQuantity() < $data['quantity']) {
            return $this->json(['error' => 'Not enough stock'], 409);
        }

        $product->setStockQuantity($product->getStockQuantity() - $data['quantity']);
        $em->flush();

        return $this->json([
            'message' => 'Stock decreased',
            'productId' => $product->getId(),
            'newQuantity' => $product->getStockQuantity()
        ]);
    }

    // Optionnel : Lister les produits bientôt en rupture de stock (seuil à adapter)
    #[Route('/{id}/stock/low', name: 'warehouse_stock_low', methods: ['GET'])]
    public function lowStock(Warehouse $warehouse, EntityManagerInterface $em): JsonResponse
    {
        $threshold = 20; // seuil d'alerte
        $products = $em->getRepository(Product::class)->findBy(['warehouse' => $warehouse]);

        $lowStock = array_filter($products, fn(Product $p) => $p->getStockQuantity() <= $threshold);

        $data = array_map(fn(Product $p) => [
            'productId' => $p->getId(),
            'productName' => $p->getProductName(),
            'stockQuantity' => $p->getStockQuantity()
        ], $lowStock);

        return $this->json($data);
    }
}

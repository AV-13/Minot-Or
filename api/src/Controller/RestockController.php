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
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/restocks')]
class RestockController extends AbstractController
{
    // Créer une commande de réapprovisionnement
    #[Route('', name: 'restock_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $required = ['supplierId', 'truckId', 'productId', 'supplierProductQuantity', 'orderNumber', 'orderDate', 'orderStatus'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }

        $supplier = $em->getRepository(Supplier::class)->find($data['supplierId']);
        $truck = $em->getRepository(Truck::class)->find($data['truckId']);
        $product = $em->getRepository(Product::class)->find($data['productId']);

        if (!$supplier || !$truck || !$product) {
            return $this->json(['error' => 'Supplier, Truck, or Product not found'], 404);
        }

        if (!OrderStatus::tryFrom($data['orderStatus'])) {
            return $this->json(['error' => 'Invalid orderStatus'], 400);
        }

        // Vérifie si la commande existe déjà
        $already = $em->getRepository(Restock::class)->findOneBy([
            'supplier' => $supplier,
            'truck' => $truck,
            'product' => $product
        ]);
        if ($already) {
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

        return $this->json([
            'message' => 'Restock created',
            'supplierId' => $supplier->getId(),
            'truckId' => $truck->getId(),
            'productId' => $product->getId()
        ], 201);
    }

    // Lister tous les restocks
    #[Route('', name: 'restock_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $restocks = $em->getRepository(Restock::class)->findAll();

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

        return $this->json($data);
    }

    // Voir le détail d’un restock (clé composite)
    #[Route('/{supplierId}/{truckId}/{productId}', name: 'restock_detail', methods: ['GET'])]
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
            'supplierId' => $restock->getSupplier()->getId(),
            'supplierName' => $restock->getSupplier()->getSupplierName(),
            'truckId' => $restock->getTruck()->getId(),
            'productId' => $restock->getProduct()->getId(),
            'productName' => $restock->getProduct()->getProductName(),
            'quantity' => $restock->getSupplierProductQuantity(),
            'orderNumber' => $restock->getOrderNumber(),
            'orderDate' => $restock->getOrderDate()?->format('Y-m-d'),
            'orderStatus' => $restock->getOrderStatus()?->value
        ]);
    }

    // Modifier le statut d'un restock (clé composite)
    #[Route('/{supplierId}/{truckId}/{productId}/status', name: 'restock_patch_status', methods: ['PATCH'])]
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
        return $this->json([
            'supplierId' => $restock->getSupplier()->getId(),
            'truckId' => $restock->getTruck()->getId(),
            'productId' => $restock->getProduct()->getId(),
            'orderStatus' => $restock->getOrderStatus()->value
        ]);
    }

    // Supprimer un restock (clé composite)
    #[Route('/{supplierId}/{truckId}/{productId}', name: 'restock_delete', methods: ['DELETE'])]
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

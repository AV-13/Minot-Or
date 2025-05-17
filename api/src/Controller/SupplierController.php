<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Entity\Product;
use App\Entity\ProductSupplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/suppliers')]
class SupplierController extends AbstractController
{
    // Créer un fournisseur
    #[Route('', name: 'supplier_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['supplierName']) || empty($data['supplierAddress'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }

        $supplier = new Supplier();
        $supplier->setSupplierName($data['supplierName']);
        $supplier->setSupplierAddress($data['supplierAddress']);

        $em->persist($supplier);
        $em->flush();

        return $this->json([
            'message' => 'Supplier created successfully',
            'id' => $supplier->getId()
        ], 201);
    }

    // Lister tous les fournisseurs
    #[Route('', name: 'supplier_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $suppliers = $em->getRepository(Supplier::class)->findAll();

        $data = array_map(fn(Supplier $s) => [
            'id' => $s->getId(),
            'supplierName' => $s->getSupplierName(),
            'supplierAddress' => $s->getSupplierAddress()
        ], $suppliers);

        return $this->json($data);
    }

    // Détail d'un fournisseur
    #[Route('/{id}', name: 'supplier_detail', methods: ['GET'])]
    public function detail(Supplier $supplier): JsonResponse
    {
        return $this->json([
            'id' => $supplier->getId(),
            'supplierName' => $supplier->getSupplierName(),
            'supplierAddress' => $supplier->getSupplierAddress()
        ]);
    }

    // Modifier un fournisseur
    #[Route('/{id}', name: 'supplier_update', methods: ['PUT'])]
    public function update(Request $request, Supplier $supplier, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!empty($data['supplierName'])) {
            $supplier->setSupplierName($data['supplierName']);
        }
        if (!empty($data['supplierAddress'])) {
            $supplier->setSupplierAddress($data['supplierAddress']);
        }

        $em->flush();

        return $this->json(['message' => 'Supplier updated successfully']);
    }

    // Supprimer un fournisseur
    #[Route('/{id}', name: 'supplier_delete', methods: ['DELETE'])]
    public function delete(Supplier $supplier, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($supplier);
        $em->flush();

        return $this->json(['message' => 'Supplier deleted successfully']);
    }

    // Lister les produits d'un fournisseur
    #[Route('/{id}/products', name: 'supplier_products', methods: ['GET'])]
    public function listProducts(Supplier $supplier, EntityManagerInterface $em): JsonResponse
    {
        $repo = $em->getRepository(ProductSupplier::class);
        $associations = $repo->findBy(['supplier' => $supplier]);
        $products = array_map(fn(ProductSupplier $ps) => [
            'id' => $ps->getProduct()->getId(),
            'productName' => $ps->getProduct()->getProductName()
        ], $associations);

        return $this->json($products);
    }

    // Associer un produit à un fournisseur
    #[Route('/{id}/products', name: 'supplier_add_product', methods: ['POST'])]
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

    // Dissocier un produit d'un fournisseur
    #[Route('/{id}/products/{productId}', name: 'supplier_remove_product', methods: ['DELETE'])]
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


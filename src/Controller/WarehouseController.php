<?php

namespace App\Controller;

use App\Entity\Warehouse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class WarehouseController extends AbstractController
{
    #[Route('/api/warehouses', name: 'create_warehouse', methods: ['POST'])]
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
}

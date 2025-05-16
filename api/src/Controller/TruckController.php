<?php

namespace App\Controller;

use App\Entity\Truck;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TruckController extends AbstractController
{
//    #[Route('/truck', name: 'app_truck', methods: ['POST'])]
//    public function createTruck(Request $request, EntityManagerInterface $em): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//
//        if (empty($data['warehouseAddress']) || empty($data['storageCapacity'])) {
//            return $this->json(['error' => 'Missing fields'], 400);
//        }
//
//        $truck = new Truck();
//        $truck
//            ->setDeliveryCount($data['deliveryCount'])
//            ->setDriver($data['driverId'])
//            ->setTruckType($data['truckType'])
//
//        $em->persist($truck);
//        $em->flush();
//
//        return $this->json([
//            'message' => 'Warehouse created successfully',
//            'id' => $truck->getId()
//        ], 201);
//    }
}

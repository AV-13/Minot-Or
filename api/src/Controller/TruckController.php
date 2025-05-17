<?php

namespace App\Controller;

use App\Entity\Truck;
use App\Entity\Warehouse;
use App\Entity\Clean;
use App\Entity\TruckCleaning;
use App\Enum\TruckCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/trucks')]
class TruckController extends AbstractController
{
    // Créer un camion
    #[Route('', name: 'truck_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $required = [
            'registrationNumber', 'truckType', 'isAvailable',
            'deliveryCount', 'transportDistance', 'transportFee', 'idWarehouse'
        ];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }

        if (!TruckCategory::tryFrom($data['truckType'])) {
            return $this->json(['error' => 'Invalid truckType'], 400);
        }

        $warehouse = $em->getRepository(Warehouse::class)->find($data['idWarehouse']);
        if (!$warehouse) {
            return $this->json(['error' => 'Warehouse not found'], 404);
        }

        $truck = new Truck();
        $truck->setRegistrationNumber($data['registrationNumber']);
        $truck->setTruckType(TruckCategory::from($data['truckType']));
        $truck->setIsAvailable((bool)$data['isAvailable']);
        $truck->setDeliveryCount((int)$data['deliveryCount']);
        $truck->setTransportDistance((float)$data['transportDistance']);
        $truck->setTransportFee((float)$data['transportFee']);
        $truck->setWarehouse($warehouse);

        $em->persist($truck);
        $em->flush();

        return $this->json([
            'message' => 'Truck created successfully',
            'id' => $truck->getId()
        ], 201);
    }

    // Lister tous les camions
    #[Route('', name: 'truck_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $trucks = $em->getRepository(Truck::class)->findAll();

        $data = array_map(fn(Truck $t) => [
            'id' => $t->getId(),
            'registrationNumber' => $t->getRegistrationNumber(),
            'truckType' => $t->getTruckType()?->value,
            'isAvailable' => $t->isAvailable(),
            'deliveryCount' => $t->getDeliveryCount(),
            'transportDistance' => $t->getTransportDistance(),
            'transportFee' => $t->getTransportFee(),
            'warehouseId' => $t->getWarehouse()?->getId(),
        ], $trucks);

        return $this->json($data);
    }

    // Détail d'un camion
    #[Route('/{id}', name: 'truck_detail', methods: ['GET'])]
    public function detail(Truck $truck): JsonResponse
    {
        return $this->json([
            'id' => $truck->getId(),
            'registrationNumber' => $truck->getRegistrationNumber(),
            'truckType' => $truck->getTruckType()?->value,
            'isAvailable' => $truck->isAvailable(),
            'deliveryCount' => $truck->getDeliveryCount(),
            'transportDistance' => $truck->getTransportDistance(),
            'transportFee' => $truck->getTransportFee(),
            'warehouseId' => $truck->getWarehouse()?->getId(),
        ]);
    }

    // Modifier un camion
    #[Route('/{id}', name: 'truck_update', methods: ['PUT'])]
    public function update(Request $request, Truck $truck, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['registrationNumber'])) $truck->setRegistrationNumber($data['registrationNumber']);
        if (isset($data['truckType'])) {
            if (!TruckCategory::tryFrom($data['truckType'])) {
                return $this->json(['error' => 'Invalid truckType'], 400);
            }
            $truck->setTruckType(TruckCategory::from($data['truckType']));
        }
        if (isset($data['isAvailable'])) $truck->setIsAvailable((bool)$data['isAvailable']);
        if (isset($data['deliveryCount'])) $truck->setDeliveryCount((int)$data['deliveryCount']);
        if (isset($data['transportDistance'])) $truck->setTransportDistance((float)$data['transportDistance']);
        if (isset($data['transportFee'])) $truck->setTransportFee((float)$data['transportFee']);
        if (isset($data['idWarehouse'])) {
            $warehouse = $em->getRepository(Warehouse::class)->find($data['idWarehouse']);
            if ($warehouse) $truck->setWarehouse($warehouse);
        }

        $em->flush();

        return $this->json(['message' => 'Truck updated successfully']);
    }

    // Supprimer un camion
    #[Route('/{id}', name: 'truck_delete', methods: ['DELETE'])]
    public function delete(Truck $truck, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($truck);
        $em->flush();
        return $this->json(['message' => 'Truck deleted successfully']);
    }

    // Changer la disponibilité du camion
    #[Route('/{id}/status', name: 'truck_change_status', methods: ['PATCH'])]
    public function changeStatus(Truck $truck, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['isAvailable'])) {
            return $this->json(['error' => 'Missing isAvailable'], 400);
        }

        $truck->setIsAvailable((bool)$data['isAvailable']);
        $em->flush();

        return $this->json([
            'id' => $truck->getId(),
            'isAvailable' => $truck->isAvailable()
        ]);
    }

    // Lister les camions disponibles
    #[Route('/available', name: 'truck_available', methods: ['GET'])]
    public function available(EntityManagerInterface $em): JsonResponse
    {
        $trucks = $em->getRepository(Truck::class)->findBy(['isAvailable' => true]);
        $data = array_map(fn(Truck $t) => [
            'id' => $t->getId(),
            'registrationNumber' => $t->getRegistrationNumber(),
            'truckType' => $t->getTruckType()?->value,
            'warehouseId' => $t->getWarehouse()?->getId(),
        ], $trucks);

        return $this->json($data);
    }

    // Lister l’historique des nettoyages (cleanings) d’un camion
    #[Route('/{id}/cleans', name: 'truck_cleanings', methods: ['GET'])]
    public function listCleanings(Truck $truck): JsonResponse
    {
        $cleans = $truck->getCleans();
        $data = [];
        foreach ($cleans as $clean) {
            $data[] = [
                'truckCleaningId' => $clean->getTruckCleaning()?->getId(),
                'truckId' => $clean->getTruck()?->getId(),
                // Ajoute d'autres champs utiles (par exemple dates)
                'cleaningStartDate' => $clean->getTruckCleaning()?->getCleaningStartDate()?->format('Y-m-d'),
                'cleaningEndDate' => $clean->getTruckCleaning()?->getCleaningEndDate()?->format('Y-m-d'),
                'observations' => $clean->getTruckCleaning()?->getObservations(),
            ];
        }
        return $this->json($data);
    }
}

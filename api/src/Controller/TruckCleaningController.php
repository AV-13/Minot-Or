<?php

namespace App\Controller;

use App\Entity\Truck;
use App\Entity\TruckCleaning;
use App\Entity\Clean;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cleanings')]
class TruckCleaningController extends AbstractController
{
    // Créer un cycle de nettoyage et associer un camion
    #[Route('', name: 'truck_cleaning_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Champs obligatoires pour TruckCleaning + le camion à associer
        $required = ['cleaningStartDate', 'cleaningEndDate', 'observations', 'truckId'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }

        $truck = $em->getRepository(Truck::class)->find($data['truckId']);
        if (!$truck) {
            return $this->json(['error' => 'Truck not found'], 404);
        }

        // Création du cycle de nettoyage
        $truckCleaning = new TruckCleaning();
        $truckCleaning->setCleaningStartDate(new \DateTime($data['cleaningStartDate']));
        $truckCleaning->setCleaningEndDate(new \DateTime($data['cleaningEndDate']));
        $truckCleaning->setObservations($data['observations']);

        $em->persist($truckCleaning);
        $em->flush();

        // Association (table Clean)
        $clean = new Clean();
        $clean->setTruck($truck);
        $clean->setTruckCleaning($truckCleaning);

        $em->persist($clean);
        $em->flush();

        return $this->json([
            'message' => 'Cleaning created and associated with truck',
            'truckCleaningId' => $truckCleaning->getId(),
            'truckId' => $truck->getId()
        ], 201);
    }

    // Lister tous les cycles de nettoyage d’un camion donné
    #[Route('/truck/{truckId}', name: 'truck_cleanings_by_truck', methods: ['GET'])]
    public function listByTruck(int $truckId, EntityManagerInterface $em): JsonResponse
    {
        $truck = $em->getRepository(Truck::class)->find($truckId);
        if (!$truck) {
            return $this->json(['error' => 'Truck not found'], 404);
        }
        $cleans = $truck->getCleans();

        $data = [];
        foreach ($cleans as $clean) {
            $tc = $clean->getTruckCleaning();
            $data[] = [
                'truckCleaningId' => $tc->getId(),
                'startDate' => $tc->getCleaningStartDate()?->format('Y-m-d'),
                'endDate' => $tc->getCleaningEndDate()?->format('Y-m-d'),
                'observations' => $tc->getObservations()
            ];
        }
        return $this->json($data);
    }

    // Lister tous les cycles de nettoyage (tous camions)
    #[Route('', name: 'truck_cleaning_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $truckCleanings = $em->getRepository(TruckCleaning::class)->findAll();
        $data = array_map(fn(TruckCleaning $tc) => [
            'id' => $tc->getId(),
            'startDate' => $tc->getCleaningStartDate()?->format('Y-m-d'),
            'endDate' => $tc->getCleaningEndDate()?->format('Y-m-d'),
            'observations' => $tc->getObservations()
        ], $truckCleanings);

        return $this->json($data);
    }

    // (Optionnel) Supprimer une association cleaning/camion
    #[Route('/{truckCleaningId}/{truckId}', name: 'truck_cleaning_delete', methods: ['DELETE'])]
    public function delete(
        int $truckCleaningId,
        int $truckId,
        EntityManagerInterface $em
    ): JsonResponse {
        $truckCleaning = $em->getRepository(TruckCleaning::class)->find($truckCleaningId);
        $truck = $em->getRepository(Truck::class)->find($truckId);

        if (!$truckCleaning || !$truck) {
            return $this->json(['error' => 'Truck or TruckCleaning not found'], 404);
        }

        $clean = $em->getRepository(Clean::class)->findOneBy([
            'truckCleaning' => $truckCleaning,
            'truck' => $truck
        ]);
        if (!$clean) {
            return $this->json(['error' => 'Association not found'], 404);
        }

        $em->remove($clean);
        $em->flush();

        // Si tu veux supprimer le TruckCleaning uniquement s'il n'est plus lié à aucun truck :
        // $remaining = $em->getRepository(Clean::class)->findBy(['truckCleaning' => $truckCleaning]);
        // if (count($remaining) === 0) {
        //     $em->remove($truckCleaning);
        //     $em->flush();
        // }

        return $this->json(['message' => 'Cleaning/truck association deleted']);
    }
}

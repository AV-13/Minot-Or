<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Entity\SalesList;
use App\Enum\DeliveryStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class DeliveryController extends AbstractController
{
    #[Route('/api/salesLists/{id}/delivery', name: 'salesList_delivery_create', methods: ['POST'])]
    public function createDelivery(
        SalesList $salesList,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        // Empêcher la création si déjà livrée ou déjà une livraison existe (unique constraint)
        if ($salesList->getDelivery()) {
            return $this->json(['error' => 'Delivery already exists for this SalesList'], 409);
        }
        $data = json_decode($request->getContent(), true);

        // Champs attendus
        $required = ['deliveryDate', 'deliveryAddress', 'deliveryNumber'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }

        $delivery = new Delivery();
        $delivery->setDeliveryDate(new \DateTime($data['deliveryDate']));
        $delivery->setDeliveryAddress($data['deliveryAddress']);
        $delivery->setDeliveryNumber($data['deliveryNumber']);
        $delivery->setDeliveryStatus(\App\Enum\DeliveryStatus::InPreparation);
        $delivery->setQrCode('QR-' . uniqid());
        $delivery->setSalesList($salesList);

        $em->persist($delivery);
        $em->flush();

        return $this->json([
            'id' => $delivery->getId(),
            'deliveryDate' => $delivery->getDeliveryDate()->format('Y-m-d'),
            'deliveryStatus' => $delivery->getDeliveryStatus()->value,
            'deliveryAddress' => $delivery->getDeliveryAddress(),
            'deliveryNumber' => $delivery->getDeliveryNumber(),
            'qrCode' => $delivery->getQrCode(),
        ], 201);
    }
    #[Route('/api/salesLists/{id}/delivery', name: 'salesList_delivery_get', methods: ['GET'])]
    public function getDelivery(SalesList $salesList): JsonResponse
    {
        $delivery = $salesList->getDelivery();
        if (!$delivery) {
            return $this->json(['error' => 'No delivery for this SalesList'], 404);
        }
        return $this->json([
            'id' => $delivery->getId(),
            'deliveryDate' => $delivery->getDeliveryDate()->format('Y-m-d'),
            'deliveryStatus' => $delivery->getDeliveryStatus()->value,
            'deliveryAddress' => $delivery->getDeliveryAddress(),
            'deliveryNumber' => $delivery->getDeliveryNumber(),
            'driverRemark' => $delivery->getDriverRemark(),
            'qrCode' => $delivery->getQrCode(),
        ]);
    }
    #[Route('/api/deliveries/{id}/status', name: 'delivery_status_patch', methods: ['PATCH'])]
    public function patchStatus(
        Delivery $delivery,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (empty($data['deliveryStatus'])) {
            return $this->json(['error' => 'Missing deliveryStatus'], 400);
        }
        if (!\App\Enum\DeliveryStatus::tryFrom($data['deliveryStatus'])) {
            return $this->json(['error' => 'Invalid deliveryStatus'], 400);
        }
        $delivery->setDeliveryStatus(\App\Enum\DeliveryStatus::from($data['deliveryStatus']));
        $em->flush();

        return $this->json([
            'id' => $delivery->getId(),
            'deliveryStatus' => $delivery->getDeliveryStatus()->value
        ]);
    }
    #[Route('/api/deliveries/{id}/remark', name: 'delivery_remark_patch', methods: ['PATCH'])]
    public function patchRemark(
        Delivery $delivery,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['driverRemark'])) {
            return $this->json(['error' => 'Missing driverRemark'], 400);
        }
        $delivery->setDriverRemark($data['driverRemark']);
        $em->flush();

        return $this->json([
            'id' => $delivery->getId(),
            'driverRemark' => $delivery->getDriverRemark()
        ]);
    }
}

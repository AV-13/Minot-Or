<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\SalesList;
use App\Entity\Pricing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class InvoiceController extends AbstractController
{
    // Générer une facture pour une SalesList
    #[Route('/salesLists/{id}/invoice', name: 'salesList_invoice_create', methods: ['POST'])]
    public function createInvoice(
        SalesList $salesList,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse
    {
        // Vérifier qu'il n'y a pas déjà de facture
        if ($salesList->getInvoices()) {
            return $this->json(['error' => 'Invoice already exists for this SalesList'], 409);
        }

        $data = json_decode($request->getContent(), true);

        // Champs obligatoires
        $required = ['dueDate'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Missing field $field"], 400);
            }
        }
        $pricing = $em->getRepository(Pricing::class)
            ->createQueryBuilder('p')
            ->orderBy('p.modificationDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$pricing) {
            return $this->json(['error' => 'No pricing available in database'], 404);
        }
        // Calcul de la somme des produits dans la SalesList
        $totalProducts = 0;
        foreach ($salesList->getContains() as $contain) {
            $totalProducts += $contain->getProductQuantity() * $contain->getProduct()->getNetPrice();
        }

        // Distance - à adapter (ici 10km en test, tu peux le passer dans le body)
        $distance = $data['distance'] ?? 10;
        $fixedFee = $pricing->getFixedFee();
        $costPerKm = $pricing->getCostPerKm();
        $globalDiscount = $salesList->getGlobalDiscount() ?? 0;
        // Calcul du montant total (à affiner si besoin)
        $totalAmount = $totalProducts + $fixedFee + ($costPerKm * $distance) - $globalDiscount;

        $invoice = new Invoice();
        $invoice->setTotalAmount($totalAmount);
        $invoice->setIssueDate(new \DateTime()); // aujourd'hui
        $invoice->setDueDate(new \DateTime($data['dueDate']));
        $invoice->setPaymentStatus(false);
        $invoice->setAcceptanceDate(new \DateTime());
        $invoice->setSalesList($salesList);
        $invoice->setPricing($pricing);

        // Synchroniser la relation bidirectionnelle (optionnel, mais recommandé)
        $salesList->setInvoices($invoice);

        // Récupérer le Pricing le plus récent
        $em->persist($invoice);
        $em->flush();

        return $this->json([
            'id' => $invoice->getId(),
            'totalAmount' => $invoice->getTotalAmount(),
            'issueDate' => $invoice->getIssueDate()?->format('Y-m-d'),
            'dueDate' => $invoice->getDueDate()?->format('Y-m-d'),
            'paymentStatus' => $invoice->isPaymentStatus(),
            'acceptanceDate' => $invoice->getAcceptanceDate()?->format('Y-m-d'),
            'salesListId' => $salesList->getId(),
            'pricingId' => $invoice->getPricing()?->getId(),
        ], 201);
    }

    // Récupérer la facture associée à une SalesList
    #[Route('/salesLists/{id}/invoice', name: 'salesList_invoice_get', methods: ['GET'])]
    public function getInvoice(SalesList $salesList): JsonResponse
    {
        $invoice = $salesList->getInvoices();
        if (!$invoice) {
            return $this->json(['error' => 'No invoice for this SalesList'], 404);
        }
        return $this->json([
            'id' => $invoice->getId(),
            'totalAmount' => $invoice->getTotalAmount(),
            'issueDate' => $invoice->getIssueDate()?->format('Y-m-d'),
            'dueDate' => $invoice->getDueDate()?->format('Y-m-d'),
            'paymentStatus' => $invoice->isPaymentStatus(),
            'acceptanceDate' => $invoice->getAcceptanceDate()?->format('Y-m-d'),
            'salesListId' => $invoice->getSalesList()?->getId(),
            'pricingId' => $invoice->getPricing()?->getId(),
        ]);
    }

    // Marquer une facture comme payée
    #[Route('/invoices/{id}/pay', name: 'invoice_pay', methods: ['PATCH'])]
    public function payInvoice(
        Invoice $invoice,
        EntityManagerInterface $em
    ): JsonResponse
    {
        if ($invoice->isPaymentStatus()) {
            return $this->json(['message' => 'Already paid']);
        }
        $invoice->setPaymentStatus(true);
        $em->flush();

        return $this->json([
            'id' => $invoice->getId(),
            'paymentStatus' => $invoice->isPaymentStatus()
        ]);
    }
}

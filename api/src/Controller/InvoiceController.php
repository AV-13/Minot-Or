<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\SalesList;
use App\Repository\InvoiceRepository;
use App\Repository\PricingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Invoice")]
#[Route('/api/invoices')]
class InvoiceController extends AbstractController
{
    /**
     * Returns a paginated list of invoices.
     */
    #[OA\Get(
        path: '/api/invoices',
        summary: 'List all invoices (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of invoices')
        ]
    )]
    #[Route('', name: 'invoice_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, InvoiceRepository $repo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $invoices = $repo->findBy([], ['issueDate' => 'DESC'], $limit, $offset);

        $data = array_map(fn(Invoice $i) => [
            'id' => $i->getId(),
            'totalAmount' => $i->getTotalAmount(),
            'issueDate' => $i->getIssueDate()?->format('Y-m-d'),
            'dueDate' => $i->getDueDate()?->format('Y-m-d'),
            'paymentStatus' => $i->isPaymentStatus(),
            'acceptanceDate' => $i->getAcceptanceDate()?->format('Y-m-d'),
            'salesListId' => $i->getSalesList()?->getId(),
            'pricingId' => $i->getPricing()?->getId(),
        ], $invoices);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Creates an invoice for a sales list.
     */
    #[OA\Post(
        path: '/api/salesLists/{id}/invoice',
        summary: 'Create an invoice for a sales list',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['dueDate'],
                properties: [
                    new OA\Property(property: 'dueDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'distance', type: 'number')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 201, description: 'Invoice created'),
            new OA\Response(response: 409, description: 'Invoice already exists')
        ]
    )]
    #[Route('/../salesLists/{id}/invoice', name: 'salesList_invoice_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        SalesList $salesList,
        Request $request,
        EntityManagerInterface $em,
        PricingRepository $pricingRepo
    ): JsonResponse {
        if ($salesList->getInvoices()) {
            return $this->json(['error' => 'Invoice already exists for this SalesList'], 409);
        }
        $data = json_decode($request->getContent(), true);
        if (empty($data['dueDate'])) {
            return $this->json(['error' => 'Missing field dueDate'], 400);
        }
        $pricing = $pricingRepo->findOneBy([], ['modificationDate' => 'DESC']);
        if (!$pricing) {
            return $this->json(['error' => 'No pricing available in database'], 404);
        }
        $totalProducts = 0;
        foreach ($salesList->getContains() as $contain) {
            $totalProducts += $contain->getProductQuantity() * $contain->getProduct()->getNetPrice();
        }
        $distance = $data['distance'] ?? 10;
        $fixedFee = $pricing->getFixedFee();
        $costPerKm = $pricing->getCostPerKm();
        $globalDiscount = $salesList->getGlobalDiscount() ?? 0;
        $totalAmount = $totalProducts + $fixedFee + ($costPerKm * $distance) - $globalDiscount;

        $invoice = new Invoice();
        $invoice->setTotalAmount($totalAmount);
        $invoice->setIssueDate(new \DateTime());
        $invoice->setDueDate(new \DateTime($data['dueDate']));
        $invoice->setPaymentStatus(false);
        $invoice->setAcceptanceDate(new \DateTime());
        $invoice->setSalesList($salesList);
        $invoice->setPricing($pricing);

        $salesList->setInvoices($invoice);

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

    /**
     * Returns the details of an invoice.
     */
    #[OA\Get(
        path: '/api/invoices/{id}',
        summary: 'Get invoice details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Invoice details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'invoice_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(Invoice $invoice = null): JsonResponse
    {
        if (!$invoice) {
            return $this->json(['error' => 'Invoice not found'], 404);
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

    /**
     * Updates an invoice.
     */
    #[OA\Put(
        path: '/api/invoices/{id}',
        summary: 'Update an invoice',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'dueDate', type: 'string', format: 'date'),
                    new OA\Property(property: 'paymentStatus', type: 'boolean')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Invoice updated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'invoice_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Invoice $invoice = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$invoice) {
            return $this->json(['error' => 'Invoice not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (isset($data['dueDate'])) {
            $invoice->setDueDate(new \DateTime($data['dueDate']));
        }
        if (isset($data['paymentStatus'])) {
            $invoice->setPaymentStatus((bool)$data['paymentStatus']);
        }
        $em->flush();
        return $this->json(['message' => 'Invoice updated successfully']);
    }

    /**
     * Deletes an invoice.
     */
    #[OA\Delete(
        path: '/api/invoices/{id}',
        summary: 'Delete an invoice',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Invoice deleted'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'invoice_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Invoice $invoice = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$invoice) {
            return $this->json(['error' => 'Invoice not found'], 404);
        }
        $em->remove($invoice);
        $em->flush();
        return $this->json(['message' => 'Invoice deleted successfully']);
    }

    /**
     * Marks an invoice as paid.
     */
    #[OA\Patch(
        path: '/api/invoices/{id}/pay',
        summary: 'Mark an invoice as paid',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Invoice marked as paid'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}/pay', name: 'invoice_pay', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function pay(Invoice $invoice = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$invoice) {
            return $this->json(['error' => 'Invoice not found'], 404);
        }
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
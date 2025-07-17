<?php

namespace App\Controller;

use App\Entity\Quotation;
use App\Entity\SalesList;
use App\Repository\QuotationRepository;
use App\Repository\PricingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Quotation")]
#[Route('/api/quotations')]
class QuotationController extends AbstractController
{
    /**
     * Renvoie une liste paginée des devis avec informations client pour l'admin.
     */
    #[Route('/admin', name: 'quotation_admin_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function adminList(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        // Filtres optionnels
        $searchTerm = $request->query->get('term');
        $dateFrom = $request->query->get('dateFrom');
        $dateTo = $request->query->get('dateTo');
        $status = $request->query->get('status');

        // Construction de la requête
        $qb = $em->createQueryBuilder();
        $qb->select('q, s, e, u')
           ->from('App\Entity\Quotation', 'q')
           ->leftJoin('q.salesList', 's')
           ->leftJoin('s.evaluates', 'e')
           ->leftJoin('e.reviewer', 'u')
           ->orderBy('q.issueDate', 'DESC');

        // Appliquer les filtres
        if ($searchTerm) {
            $qb->andWhere('u.email LIKE :term OR u.firstName LIKE :term OR u.lastName LIKE :term')
               ->setParameter('term', '%' . $searchTerm . '%');
        }

        if ($dateFrom) {
            $qb->andWhere('q.issueDate >= :dateFrom')
               ->setParameter('dateFrom', new \DateTime($dateFrom));
        }

        if ($dateTo) {
            $qb->andWhere('q.issueDate <= :dateTo')
               ->setParameter('dateTo', new \DateTime($dateTo));
        }

        if ($status && $status !== 'all') {
            $qb->andWhere('s.status = :status')
               ->setParameter('status', $status);
        }

        // Comptage total pour pagination
        $totalQb = clone $qb;
        $totalQb->select('COUNT(DISTINCT q.id)');
        $total = $totalQb->getQuery()->getSingleScalarResult();

        // Pagination
        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $quotations = $qb->getQuery()->getResult();

        // Formatage des données
        $data = array_map(function($quotation) {
            /** @var Quotation $quotation */
            $client = null;
            $salesList = $quotation->getSalesList();
            $debugInfo = [];

            $debugInfo['hasSalesList'] = $salesList !== null;

            if ($salesList) {
                $evaluates = $salesList->getEvaluates();
                $debugInfo['evaluatesCount'] = $evaluates->count();
                $debugInfo['evaluatesEmpty'] = $evaluates->isEmpty();

                if (!$evaluates->isEmpty()) {
                    $evaluate = $evaluates->first();
                    $debugInfo['evaluateExists'] = $evaluate !== null;

                    if ($evaluate) {
                        $user = $evaluate->getReviewer();
                        $debugInfo['hasReviewer'] = $user !== null;

                        if ($user) {
                            $client = [
                                'id' => $user->getId(),
                                'email' => $user->getEmail(),
                                'firstName' => $user->getFirstName(),
                                'lastName' => $user->getLastName(),
                                'quoteAccepted' => $evaluate->isQuoteAccepted()
                            ];
                        }
                    }
                }
            }

            return [
                'id' => $quotation->getId(),
                'totalAmount' => $quotation->getTotalAmount(),
                'issueDate' => $quotation->getIssueDate()?->format('Y-m-d'),
                'dueDate' => $quotation->getDueDate()?->format('Y-m-d'),
                'paymentStatus' => $quotation->isPaymentStatus(),
                'salesListId' => $salesList?->getId(),
                'salesListStatus' => $salesList?->getStatus()?->value,
                'client' => $client,
                'debug' => $debugInfo
            ];
        }, $quotations);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }
    /**
     * Returns a paginated list of quotations.
     */
    #[OA\Get(
        path: '/api/quotations',
        summary: 'List all quotations (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of quotations')
        ]
    )]
    #[Route('', name: 'quotation_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, QuotationRepository $repo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $repo->count([]);
        $quotations = $repo->findBy([], ['issueDate' => 'DESC'], $limit, $offset);

        $data = array_map(fn(Quotation $i) => [
            'id' => $i->getId(),
            'totalAmount' => $i->getTotalAmount(),
            'issueDate' => $i->getIssueDate()?->format('Y-m-d'),
            'dueDate' => $i->getDueDate()?->format('Y-m-d'),
            'paymentStatus' => $i->isPaymentStatus(),
            'acceptanceDate' => $i->getAcceptanceDate()?->format('Y-m-d'),
            'salesListId' => $i->getSalesList()?->getId(),
            'pricingId' => $i->getPricing()?->getId(),
        ], $quotations);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Creates an quotation for a sales list.
     */
    #[OA\Post(
        path: '/salesLists/{id}/quotation',
        summary: 'Create an quotation for a sales list',
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
            new OA\Response(response: 201, description: 'quotation created'),
            new OA\Response(response: 409, description: 'quotation already exists')
        ]
    )]
    #[Route('/salesLists/{id}/quotation', name: 'salesList_quotation_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        SalesList $salesList,
        Request $request,
        EntityManagerInterface $em,
        PricingRepository $pricingRepo
    ): JsonResponse {
        if ($salesList->getQuotations()) {
            return $this->json(['error' => 'Quotation already exists for this SalesList'], 409);
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

        $quotation = new Quotation();
        $quotation->setTotalAmount($totalAmount);
        $quotation->setIssueDate(new \DateTime());
        $quotation->setDueDate(new \DateTime($data['dueDate']));
        $quotation->setPaymentStatus(false);
        $quotation->setAcceptanceDate(new \DateTime());
        $quotation->setSalesList($salesList);
        $quotation->setPricing($pricing);

        $salesList->setQuotations($quotation);

        $em->persist($quotation);
        $em->flush();

        return $this->json([
            'id' => $quotation->getId(),
            'totalAmount' => $quotation->getTotalAmount(),
            'issueDate' => $quotation->getIssueDate()?->format('Y-m-d'),
            'dueDate' => $quotation->getDueDate()?->format('Y-m-d'),
            'paymentStatus' => $quotation->isPaymentStatus(),
            'acceptanceDate' => $quotation->getAcceptanceDate()?->format('Y-m-d'),
            'salesListId' => $salesList->getId(),
            'pricingId' => $quotation->getPricing()?->getId(),
        ], 201);
    }

    /**
     * Returns the details of an quotation.
     */
    #[OA\Get(
        path: '/api/quotations/{id}',
        summary: 'Get quotation details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Quotation details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'quotation_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(Quotation $quotation = null): JsonResponse
    {
        if (!$quotation) {
            return $this->json(['error' => 'Quotation not found'], 404);
        }
        return $this->json([
            'id' => $quotation->getId(),
            'totalAmount' => $quotation->getTotalAmount(),
            'issueDate' => $quotation->getIssueDate()?->format('Y-m-d'),
            'dueDate' => $quotation->getDueDate()?->format('Y-m-d'),
            'paymentStatus' => $quotation->isPaymentStatus(),
            'acceptanceDate' => $quotation->getAcceptanceDate()?->format('Y-m-d'),
            'salesListId' => $quotation->getSalesList()?->getId(),
            'pricingId' => $quotation->getPricing()?->getId(),
        ]);
    }

    /**
     * Updates an quotation.
     */
    #[OA\Put(
        path: '/api/quotations/{id}',
        summary: 'Update an quotation',
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
            new OA\Response(response: 200, description: 'Quotation updated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'quotation_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Quotation $quotation = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$quotation) {
            return $this->json(['error' => 'Quotation not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        if (isset($data['dueDate'])) {
            $quotation->setDueDate(new \DateTime($data['dueDate']));
        }
        if (isset($data['paymentStatus'])) {
            $quotation->setPaymentStatus((bool)$data['paymentStatus']);
        }
        $em->flush();
        return $this->json(['message' => 'Quotation updated successfully']);
    }

    /**
     * Deletes an quotation.
     */
    #[OA\Delete(
        path: '/api/quotations/{id}',
        summary: 'Delete an quotation',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Quotation deleted'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'quotation_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_SALES')]
    public function delete(Quotation $quotation = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$quotation) {
            return $this->json(['error' => 'Quotation not found'], 404);
        }
        $em->remove($quotation);
        $em->flush();
        return $this->json(['message' => 'Quotation deleted successfully']);
    }

    /**
     * Marks an quotation as paid.
     */
    #[OA\Patch(
        path: '/api/quotations/{id}/pay',
        summary: 'Mark an quotation as paid',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Quotation marked as paid'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}/pay', name: 'quotation_pay', methods: ['PATCH'])]
    #[IsGranted('ROLE_USER')]
    public function pay(Quotation $quotation = null, EntityManagerInterface $em): JsonResponse
    {
        if (!$quotation) {
            return $this->json(['error' => 'Quotation not found'], 404);
        }
        if ($quotation->isPaymentStatus()) {
            return $this->json(['message' => 'Already paid']);
        }
        $quotation->setPaymentStatus(true);
        $em->flush();
        return $this->json([
            'id' => $quotation->getId(),
            'paymentStatus' => $quotation->isPaymentStatus()
        ]);
    }
}
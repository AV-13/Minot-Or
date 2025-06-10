<?php

namespace App\Controller;

use App\Entity\Evaluate;
use App\Entity\SalesList;
use App\Entity\User;
use App\Repository\EvaluateRepository;
use App\Repository\SalesListRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Evaluate")]
#[Route('/api/evaluates')]
class EvaluateController extends AbstractController
{
    /**
     * Creates a new evaluation (accept or refuse a quote).
     */
    #[OA\Post(
        path: '/api/evaluates',
        summary: 'Create a new evaluation (accept or refuse a quote)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['salesListId', 'userId', 'quoteAccepted'],
                properties: [
                    new OA\Property(property: 'salesListId', type: 'integer'),
                    new OA\Property(property: 'userId', type: 'integer'),
                    new OA\Property(property: 'quoteAccepted', type: 'boolean')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Evaluation created'),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 404, description: 'SalesList or User not found'),
            new OA\Response(response: 409, description: 'Evaluation already exists')
        ]
    )]
    #[Route('', name: 'evaluate_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SalesListRepository $salesListRepo,
        UserRepository $userRepo,
        EvaluateRepository $evalRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['salesListId'], $data['userId'], $data['quoteAccepted'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        $salesList = $salesListRepo->find($data['salesListId']);
        if (!$salesList) {
            return $this->json(['error' => 'SalesList not found'], 404);
        }

        $user = $userRepo->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $existing = $evalRepo->findOneBy(['salesList' => $salesList, 'reviewer' => $user]);
        if ($existing) {
            return $this->json(['error' => 'Evaluation already exists'], 409);
        }

        $evaluate = new Evaluate();
        $evaluate->setSalesList($salesList);
        $evaluate->setReviewer($user);
        $evaluate->setQuoteAccepted((bool)$data['quoteAccepted']);

        $em->persist($evaluate);
        $em->flush();

        return $this->json([
            'message' => 'Evaluation created successfully',
            'salesListId' => $salesList->getId(),
            'userId' => $user->getId(),
            'quoteAccepted' => $evaluate->isQuoteAccepted()
        ], 201);
    }

    /**
     * Returns a paginated list of all evaluations.
     */
    #[OA\Get(
        path: '/api/evaluates',
        summary: 'List all evaluations (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of evaluations')
        ]
    )]
    #[Route('', name: 'evaluate_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request, EvaluateRepository $evalRepo): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;

        $total = $evalRepo->count([]);
        $evaluates = $evalRepo->findBy([], null, $limit, $offset);

        $data = array_map(function (Evaluate $e) {
            return [
                'salesListId' => $e->getSalesList()?->getId(),
                'userId' => $e->getReviewer()?->getId(),
                'quoteAccepted' => $e->isQuoteAccepted()
            ];
        }, $evaluates);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Returns the details of a specific evaluation.
     */
    #[OA\Get(
        path: '/api/evaluates/{salesListId}/{userId}',
        summary: 'Get a specific evaluation',
        parameters: [
            new OA\Parameter(name: 'salesListId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Evaluation details'),
            new OA\Response(response: 404, description: 'Evaluation not found')
        ]
    )]
    #[Route('/{salesListId}/{userId}', name: 'evaluate_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(
        int $salesListId,
        int $userId,
        EvaluateRepository $evalRepo
    ): JsonResponse {
        $evaluate = $evalRepo->findOneBy([
            'salesList' => $salesListId,
            'reviewer' => $userId
        ]);
        if (!$evaluate) {
            return $this->json(['error' => 'Evaluation not found'], 404);
        }

        return $this->json([
            'salesListId' => $evaluate->getSalesList()?->getId(),
            'userId' => $evaluate->getReviewer()?->getId(),
            'quoteAccepted' => $evaluate->isQuoteAccepted()
        ]);
    }

    /**
     * Updates an evaluation.
     */
    #[OA\Put(
        path: '/api/evaluates/{salesListId}/{userId}',
        summary: 'Update an evaluation',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'quoteAccepted', type: 'boolean')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'salesListId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Evaluation updated'),
            new OA\Response(response: 404, description: 'Evaluation not found')
        ]
    )]
    #[Route('/{salesListId}/{userId}', name: 'evaluate_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(
        int $salesListId,
        int $userId,
        Request $request,
        EvaluateRepository $evalRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $evaluate = $evalRepo->findOneBy([
            'salesList' => $salesListId,
            'reviewer' => $userId
        ]);
        if (!$evaluate) {
            return $this->json(['error' => 'Evaluation not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['quoteAccepted'])) {
            return $this->json(['error' => 'Missing quoteAccepted'], 400);
        }

        $evaluate->setQuoteAccepted((bool)$data['quoteAccepted']);
        $em->flush();

        return $this->json([
            'message' => 'Evaluation updated successfully',
            'salesListId' => $evaluate->getSalesList()?->getId(),
            'userId' => $evaluate->getReviewer()?->getId(),
            'quoteAccepted' => $evaluate->isQuoteAccepted()
        ]);
    }

    /**
     * Deletes an evaluation.
     */
    #[OA\Delete(
        path: '/api/evaluates/{salesListId}/{userId}',
        summary: 'Delete an evaluation',
        parameters: [
            new OA\Parameter(name: 'salesListId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Evaluation deleted'),
            new OA\Response(response: 404, description: 'Evaluation not found')
        ]
    )]
    #[Route('/{salesListId}/{userId}', name: 'evaluate_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        int $salesListId,
        int $userId,
        EvaluateRepository $evalRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $evaluate = $evalRepo->findOneBy([
            'salesList' => $salesListId,
            'reviewer' => $userId
        ]);
        if (!$evaluate) {
            return $this->json(['error' => 'Evaluation not found'], 404);
        }

        $em->remove($evaluate);
        $em->flush();

        return $this->json(['message' => 'Evaluation deleted successfully']);
    }
}
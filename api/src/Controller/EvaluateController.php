<?php

namespace App\Controller;

use App\Entity\SalesList;
use App\Entity\User;
use App\Entity\Evaluate;
use App\Repository\EvaluateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/salesLists')]
class EvaluateController extends AbstractController
{
    #[Route('/{id}/evaluate', name: 'salesList_evaluate', methods: ['POST'])]
    public function evaluate(
        SalesList $salesList,
        Request $request,
        UserRepository $userRepo,
        EvaluateRepository $evalRepo,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userId'], $data['quoteAccepted'])) {
            return $this->json(['error' => 'Missing userId or quoteAccepted'], 400);
        }

        $user = $userRepo->find($data['userId']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        // Vérifier si déjà évalué
        $already = $evalRepo->findOneBy(['salesList' => $salesList, 'reviewer' => $user]);
        if ($already) {
            return $this->json(['error' => 'Evaluation already exists'], 409);
        }

        $evaluate = new Evaluate();
        $evaluate->setSalesList($salesList);
        $evaluate->setReviewer($user);
        $evaluate->setQuoteAccepted((bool) $data['quoteAccepted']);

        // Mettre à jour le statut du devis selon acceptation
        if ($data['quoteAccepted']) {
            $salesList->setStatus(\App\Enum\SalesStatus::PreparingProducts);
        } else {
            $salesList->setStatus(\App\Enum\SalesStatus::Pending); // Ou autre statut si refusé
        }

        $em->persist($evaluate);
        $em->flush();

        return $this->json([
            'message' => 'Evaluation enregistrée',
            'salesListId' => $salesList->getId(),
            'userId' => $user->getId(),
            'quoteAccepted' => $evaluate->isQuoteAccepted()
        ], 201);
    }
    #[Route('/{id}/evaluate/{userId}', name: 'salesList_evaluate_get', methods: ['GET'])]
    public function getEvaluate(
        SalesList $salesList,
        int $userId,
        UserRepository $userRepo,
        EvaluateRepository $evalRepo
    ): JsonResponse {
        $user = $userRepo->find($userId);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }
        $evaluate = $evalRepo->findOneBy(['salesList' => $salesList, 'reviewer' => $user]);
        if (!$evaluate) {
            return $this->json(['error' => 'No evaluation found'], 404);
        }
        return $this->json([
            'salesListId' => $salesList->getId(),
            'userId' => $user->getId(),
            'quoteAccepted' => $evaluate->isQuoteAccepted()
        ]);
    }

    #[Route('/{id}/evaluations', name: 'salesList_evaluations', methods: ['GET'])]
    public function getAllEvaluates(
        SalesList $salesList,
        EvaluateRepository $evalRepo
    ): JsonResponse {
        $evaluations = $evalRepo->findBy(['salesList' => $salesList]);
        $data = array_map(fn(Evaluate $e) => [
            'reviewerId' => $e->getReviewer()->getId(),
            'quoteAccepted' => $e->isQuoteAccepted()
        ], $evaluations);

        return $this->json($data);
    }
    #[Route('/{id}/evaluate/{reviewerId}', name: 'salesList_evaluate_delete', methods: ['DELETE'])]
    public function deleteEvaluate(
        SalesList $salesList,
        int $reviewerId,
        UserRepository $userRepo,
        EvaluateRepository $evalRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $reviewer = $userRepo->find($reviewerId);
        if (!$reviewer) {
            return $this->json(['error' => 'Reviewer not found'], 404);
        }
        $evaluate = $evalRepo->findOneBy(['salesList' => $salesList, 'reviewer' => $reviewer]);
        if (!$evaluate) {
            return $this->json(['error' => 'No evaluation found'], 404);
        }
        $em->remove($evaluate);
        $em->flush();
        return $this->json(['message' => 'Evaluation deleted']);
    }
}

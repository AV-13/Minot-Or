<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{

    #[Route('/api/createUser', name: 'api_user_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // validation rapide (à remplacer par Symfony Validator plus tard)
        if (!isset($data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['role'], $data['companyId'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }

        // vérifier que le rôle est valide
        if (!UserRole::tryFrom($data['role'])) {
            return $this->json(['error' => 'Invalid role'], 400);
        }

        $company = $em->getRepository(Company::class)->find($data['companyId']);
        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setRole(UserRole::from($data['role']));
        $user->setCompany($company);

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User created successfully',
            'id' => $user->getId()
        ], 201);
    }

    #[Route('/api/users', name: 'api_user_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $users = $em->getRepository(User::class)->findAll();

        $data = array_map(fn(User $u) => [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'firstName' => $u->getFirstName(),
            'lastName' => $u->getLastName(),
            'role' => $u->getRole()->value,
            'companyId' => $u->getCompany()->getId()
        ], $users);

        return $this->json($data);
    }
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Not authenticated'], 401);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'role' => $user->getRole()->value
        ]);
    }
}

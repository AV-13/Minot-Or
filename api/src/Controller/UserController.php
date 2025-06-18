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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "User")]
#[Route('/api/users')]
final class UserController extends AbstractController
{
    /**
     * Creates a new user.
     */
    #[OA\Post(
        path: '/api/users',
        summary: 'Create a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email','password','firstName','lastName','companyId'],
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'companyId', type: 'integer')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created'),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 409, description: 'Email already used')
        ]
    )]
    #[Route('', name: 'user_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['firstName'], $data['lastName'], $data['companyId'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Invalid email format'], 400);
        }
        $company = $em->getRepository(Company::class)->find($data['companyId']);
        if (!$company) {
            return $this->json(['error' => 'Company not found'], 404);
        }
        if ($em->getRepository(User::class)->findOneBy(['email' => $data['email']])) {
            return $this->json(['error' => 'Email already used'], 409);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setRole(UserRole::WaitingForValidation);
        $user->setRoles([$user->getRole()->toSymfonyRole()]);
        $user->setCompany($company);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User created successfully',
            'id' => $user->getId()
        ], 201);
    }

    /**
     * Returns a paginated list of users for the current company, with optional role filter.
     */
    #[OA\Get(
        path: '/api/users',
        summary: 'List users',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'role', in: 'query', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated user list')
        ]
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('', name: 'user_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $user = $security->getUser();
        $company = $user->getCompany();

        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 10));
        $role = $request->query->get('role');

        $criteria = [];
        if (!$user->getRole() || $user->getRole()->value !== 'Sales') {
            $criteria['company'] = $user->getCompany();
        }
        if ($role && UserRole::tryFrom($role)) {
            $criteria['role'] = UserRole::from($role);
        }

        $repo = $em->getRepository(User::class);
        $total = $repo->count($criteria);
        $users = $repo->findBy($criteria, [], $limit, ($page - 1) * $limit);

        $data = array_map(fn(User $u) => [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'firstName' => $u->getFirstName(),
            'lastName' => $u->getLastName(),
            'role' => $u->getRole()->value,
            'companyId' => $u->getCompany()->getId()
        ], $users);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Returns the details of a user from the current company.
     */
    #[OA\Get(
        path: '/api/users/{id}',
        summary: 'Get user details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'User details'),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'user_detail', methods: ['GET'])]
    public function detail(User $user, Security $security): JsonResponse
    {
        $current = $security->getUser();
        if ($user->getCompany()->getId() !== $current->getCompany()->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'role' => $user->getRole()->value,
            'companyId' => $user->getCompany()->getId()
        ]);
    }

    /**
     * Updates the information of a user.
     */
    #[OA\Put(
        path: '/api/users/{id}',
        summary: 'Update user',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'role', type: 'string'),
                    new OA\Property(property: 'companyId', type: 'integer')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User updated'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'user_update', methods: ['PUT'])]
    public function update(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        Security $security
    ): JsonResponse {
        $current = $security->getUser();
        if ($user->getCompany()->getId() !== $current->getCompany()->getId()) {
            return $this->json(['error' => 'Access denied'], 403);
        }
        if (!$this->isGranted('ROLE_ADMIN') && $current->getId() !== $user->getId()) {
            return $this->json(['error' => 'Only admin or the user himself can update'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json(['error' => 'Invalid email format'], 400);
            }
            $existing = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existing && $existing->getId() !== $user->getId()) {
                return $this->json(['error' => 'Email already used'], 409);
            }
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['firstName'])) $user->setFirstName($data['firstName']);
        if (isset($data['lastName'])) $user->setLastName($data['lastName']);
        if (isset($data['role']) && UserRole::tryFrom($data['role'])) {
            $user->setRole(UserRole::from($data['role']));
            $user->setRoles([$user->getRole()->toSymfonyRole()]);
        }
        if (isset($data['companyId'])) {
            $company = $em->getRepository(Company::class)->find($data['companyId']);
            if ($company) $user->setCompany($company);
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(['error' => (string) $errors], 400);
        }

        $em->flush();

        return $this->json(['message' => 'User updated successfully']);
    }

    /**
     * Deletes a user from the current company.
     */
    #[OA\Delete(
        path: '/api/users/{id}',
        summary: 'Delete user',
        responses: [
            new OA\Response(response: 200, description: 'User deleted')
        ]
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $current = $security->getUser();

//         if ($user->getCompany()->getId() !== $current->getCompany()->getId()) {
//             return $this->json(['error' => 'Access denied'], 403);
//         }

        // Si l'utilisateur courant est Sales
        if ($current->getRole()->value === 'Sales') {
            // Il ne peut pas supprimer un autre Sales
            if ($user->getRole()->value === 'Sales') {
                return $this->json(['error' => 'Impossible de supprimer un autre utilisateur Sales'], 403);
            }
        } else {
            if (!$this->isGranted('ROLE_ADMIN')) {
                return $this->json(['error' => 'Access denied'], 403);
            }
        }

        $em->remove($user);
        $em->flush();
        return $this->json(['message' => 'User deleted successfully']);
    }

    /**
     * Returns the currently authenticated user.
     */
    #[OA\Get(
        path: '/api/me',
        summary: 'Get current authenticated user',
        responses: [
            new OA\Response(response: 200, description: 'Current user')
        ]
    )]
    #[IsGranted('ROLE_USER')]
    #[Route('/api/me', name: 'user_me', methods: ['GET'])]
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
            'role' => $user->getRole()->value,
            'companyId' => $user->getCompany()->getId()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Company")]
#[Route('/api/companies')]
final class CompanyController extends AbstractController
{
    /**
     * Creates a new company.
     */
    #[OA\Post(
        path: '/api/companies',
        summary: 'Create a new company',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['companyName', 'companySiret', 'companyContact'],
                properties: [
                    new OA\Property(property: 'companyName', type: 'string', maxLength: 50),
                    new OA\Property(property: 'companySiret', type: 'string', maxLength: 50),
                    new OA\Property(property: 'companyContact', type: 'string', maxLength: 50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Company created'),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 409, description: 'Company already exists'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    #[Route('', name: 'company_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['companyName'], $data['companySiret'], $data['companyContact'])) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }
        if (mb_strlen($data['companyName']) > 50 || mb_strlen($data['companySiret']) > 50 || mb_strlen($data['companyContact']) > 50) {
            return $this->json(['error' => 'Field too long'], 400);
        }
        if (!preg_match('/^\d{14}$/', $data['companySiret'])) {
            return $this->json(['error' => 'Invalid SIRET format'], 400);
        }

        $existing = $em->getRepository(Company::class)->findOneBy(['companySiret' => $data['companySiret']]);
        if ($existing) {
            return $this->json([
                'error' => 'A company with this SIRET already exists.',
                'companyId' => $existing->getId(),
                'companyName' => $existing->getCompanyName()
            ], 409);
        }

        $company = new Company();
        $company->setCompanyName($data['companyName']);
        $company->setCompanySiret($data['companySiret']);
        $company->setCompanyContact($data['companyContact']);

        $em->persist($company);
        $em->flush();

        return $this->json([
            'message' => 'Company created successfully',
            'id' => $company->getId()
        ], 201);
    }

    /**
     * Returns a paginated list of companies.
     */
    #[OA\Get(
        path: '/api/companies',
        summary: 'List companies (paginated)',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 20))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of companies')
        ]
    )]
    #[Route('', name: 'company_list', methods: ['GET'])]
    #[IsGranted('ROLE_SALES')]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = max(1, (int)$request->query->get('limit', 20));
        $search = $request->query->get('search');
        $repo = $em->getRepository(Company::class);

        if ($search) {
            $qb = $em->createQueryBuilder()
                ->select('c')
                ->from(Company::class, 'c')
                ->where('c.companyName LIKE :search OR c.companySiret LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit);

            $companies = $qb->getQuery()->getResult();

            $total = $em->createQueryBuilder()
                ->select('COUNT(c.id)')
                ->from(Company::class, 'c')
                ->where('c.companyName LIKE :search OR c.companySiret LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()->getSingleScalarResult();
        } else {
            $total = $repo->count([]);
            $companies = $repo->findBy([], [], $limit, ($page - 1) * $limit);
        }

        $data = array_map(fn(Company $c) => [
            'id' => $c->getId(),
            'companyName' => $c->getCompanyName(),
            'companySiret' => $c->getCompanySiret(),
            'companyContact' => $c->getCompanyContact()
        ], $companies);

        return $this->json([
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $data
        ]);
    }

    /**
     * Returns the details of a company.
     */
    #[OA\Get(
        path: '/api/companies/{id}',
        summary: 'Get company details',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Company details'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    #[Route('/{id}', name: 'company_detail', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function detail(Company $company): JsonResponse
    {
        return $this->json([
            'id' => $company->getId(),
            'companyName' => $company->getCompanyName(),
            'companySiret' => $company->getCompanySiret(),
            'companyContact' => $company->getCompanyContact(),
            'unsold' => $company->isUnsold(),
        ]);
    }

    #[OA\Get(
        path: '/api/companies/siret/{siret}',
        summary: 'Obtenir une company par son numéro de Siret',
        parameters: [
            new OA\Parameter(
                name: 'siret',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la company'),
            new OA\Response(response: 404, description: 'Company non trouvée')
        ]
    )]
    #[Route('/siret/{siret}', name: 'company_get_by_siret', methods: ['GET'])]
    public function getBySiret(string $siret, EntityManagerInterface $em): JsonResponse
    {
        $company = $em->getRepository(Company::class)->findOneBy(['companySiret' => $siret]);

        if (!$company) {
            return $this->json(['error' => 'Company non trouvée'], 404);
        }

        return $this->json([
            'id' => $company->getId(),
            'companyName' => $company->getCompanyName(),
            'companySiret' => $company->getCompanySiret(),
            'companyContact' => $company->getCompanyContact()
        ]);
    }

    /**
     * Updates a company.
     */
    #[OA\Put(
        path: '/api/companies/{id}',
        summary: 'Update a company',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'companyName', type: 'string', maxLength: 50),
                    new OA\Property(property: 'companySiret', type: 'string', maxLength: 50),
                    new OA\Property(property: 'companyContact', type: 'string', maxLength: 50)
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Company updated'),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 409, description: 'SIRET already exists')
        ]
    )]
    #[Route('/{id}', name: 'company_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Company $company, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['companyName'])) {
            if (mb_strlen($data['companyName']) > 50) {
                return $this->json(['error' => 'companyName too long'], 400);
            }
            $company->setCompanyName($data['companyName']);
        }
        if (isset($data['companyContact'])) {
            if (mb_strlen($data['companyContact']) > 50) {
                return $this->json(['error' => 'companyContact too long'], 400);
            }
            $company->setCompanyContact($data['companyContact']);
        }
        if (isset($data['companySiret'])) {
            if (!preg_match('/^\d{14}$/', $data['companySiret'])) {
                return $this->json(['error' => 'Invalid SIRET format'], 400);
            }
            $existing = $em->getRepository(Company::class)->findOneBy(['companySiret' => $data['companySiret']]);
            if ($existing && $existing->getId() !== $company->getId()) {
                return $this->json(['error' => 'SIRET already exists'], 409);
            }
            $company->setCompanySiret($data['companySiret']);
        }

        $em->flush();

        return $this->json(['message' => 'Company updated successfully']);
    }

    /**
     * Deletes a company (and all its users).
     */
    #[OA\Delete(
        path: '/api/companies/{id}',
        summary: 'Delete a company (and all its users)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Company deleted'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]
    #[Route('/{id}', name: 'company_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Company $company, EntityManagerInterface $em): JsonResponse
    {
        $users = $company->getUsers();
        $userCount = $users ? count($users) : 0;

        $em->remove($company);
        $em->flush();

        return $this->json([
            'message' => "Company and all associated users have been deleted.",
            'deletedUsers' => $userCount
        ]);
    }
    /**
     * Met à jour le statut des invendus d'une entreprise.
     */
    #[OA\Patch(
        path: '/api/companies/{id}/unsold',
        summary: 'Mettre à jour le statut des invendus',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['unsold'],
                properties: [
                    new OA\Property(property: 'unsold', type: 'boolean')
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Statut des invendus mis à jour'),
            new OA\Response(response: 400, description: 'Requête invalide'),
            new OA\Response(response: 403, description: 'Accès refusé'),
            new OA\Response(response: 404, description: 'Entreprise non trouvée')
        ]
    )]
    #[Route('/{id}/unsold', name: 'company_update_unsold', methods: ['PATCH'])]
    #[IsGranted('ROLE_BAKER')]
    public function updateUnsold(Request $request, Company $company, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['unsold']) || !is_bool($data['unsold'])) {
            return $this->json(['error' => 'Le champ unsold est requis et doit être un booléen'], 400);
        }

        // Vérifier que l'utilisateur est associé à cette entreprise
        $user = $this->getUser();
        if ($user->getCompany()->getId() !== $company->getId()) {
            return $this->json(['error' => 'Vous n\'êtes pas autorisé à modifier cette entreprise'], 403);
        }

        $company->setUnsold($data['unsold']);
        $em->flush();

        return $this->json([
            'message' => 'Statut des invendus mis à jour avec succès',
            'unsold' => $company->isUnsold()
        ]);
    }
}
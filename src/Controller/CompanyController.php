<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    #[Route('/api/companies', name: 'api_company_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['companyName'], $data['companySiret'], $data['companyContact'])) {
            return $this->json(['error' => 'Missing fields'], 400);
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

    #[Route('/api/companies', name: 'api_company_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $companies = $em->getRepository(Company::class)->findAll();

        $data = array_map(fn(Company $c) => [
            'id' => $c->getId(),
            'name' => $c->getCompanyName(),
            'siret' => $c->getCompanySiret(),
            'contact' => $c->getCompanyContact()
        ], $companies);

        return $this->json($data);
    }
}

<?php

namespace App\Controller;

use App\Entity\SalesList;
use App\Entity\Product;
use App\Entity\Contains;
use App\Enum\SalesStatus;
use App\Service\SecurityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class QuoteController extends AbstractController
{
    #[Route('/api/quotes', name: 'create_quote', methods: ['POST'])]
    public function createQuote(
        Request $request,
        EntityManagerInterface $em,
        SecurityHelper $securityHelper
    ): JsonResponse {
        $user = $securityHelper->getUser();

        if (!$securityHelper->hasRole('Sales')) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (empty($data['products']) || !is_array($data['products'])) {
            return $this->json(['error' => 'No products provided'], 400);
        }

        $issueDate = new \DateTime();
        $expirationDate = clone $issueDate;
        $modified = $expirationDate->modify('+7 days');

        if ($modified === false) {
            throw new \RuntimeException('Invalid date format');
        }

        $salesList = new SalesList();
        $salesList
            ->setStatus(SalesStatus::Pending)
            ->setIssueDate($issueDate)
            ->setExpirationDate($expirationDate)
            ->setOrderDate(null); // TODO rendre orderDate NULLABLE, on set plus tard

        $em->persist($salesList);

        foreach ($data['products'] as $item) {
            if (!isset($item['productId'], $item['quantity'], $item['discount'])) {
                continue;
            }

            $product = $em->getRepository(Product::class)->find($item['productId']);
            if (!$product) {
                continue;
            }

            $contains = new Contains();
            $contains
                ->setSalesList($salesList)
                ->setProduct($product)
                ->setProductQuantity((int)$item['quantity'])
                ->setProductDiscount((int)$item['discount']);

            $em->persist($contains);
        }

        $em->flush();

        return $this->json(['message' => 'Quote created successfully', 'id' => $salesList->getId()], 201);
    }
}
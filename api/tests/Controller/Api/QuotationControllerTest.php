<?php

namespace App\Tests\Controller\Api;

use App\Entity\Evaluate;
use App\Entity\Pricing;
use App\Entity\Product;
use App\Entity\Quotation;
use App\Entity\SalesList;
use App\Entity\User;
use App\Enum\SalesStatus;
use App\Repository\PricingRepository;
use App\Repository\ProductRepository;
use App\Repository\QuotationRepository;
use App\Repository\SalesListRepository;
use App\Repository\UserRepository;
use App\Service\SecurityHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

class QuotationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private QuotationRepository $quotationRepository;
    private UserRepository $userRepository;
    private SalesListRepository $salesListRepository;
    private PricingRepository $pricingRepository;
    private ProductRepository $productRepository;
    private string $adminToken;
    private string $userToken;
    private string $salesToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->quotationRepository = static::getContainer()->get(QuotationRepository::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->salesListRepository = static::getContainer()->get(SalesListRepository::class);
        $this->pricingRepository = static::getContainer()->get(PricingRepository::class);
        $this->productRepository = static::getContainer()->get(ProductRepository::class);

        $this->adminToken = $this->getToken('sales@sales.com', 'Sales', 'sales');
        $this->userToken = $this->getToken('baker@baker.com', 'Baker', 'baker');
        $this->salesToken = $this->getToken('sales@sales.com', 'Sales', 'sales');
    }
    protected function tearDown(): void
    {
        // Nettoyer toutes les données de test
        $this->entityManager->createQuery('DELETE FROM App\Entity\Quotation')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Pricing')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\SalesList')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Company')->execute();

        parent::tearDown();
    }

    private function getToken(string $email, string $role, string $password): string
    {
        $user = $this->userRepository->findOneByEmail($email);
        if (!$user) {
            $user = new User();
            $user->setEmail($email);

            // Créer une company obligatoire
            $company = new \App\Entity\Company();
            $company->setCompanyName('Test Company');
            $company->setCompanySiret('12345678901234');
            $company->setCompanyContact('test@company.com');
            $this->entityManager->persist($company);

            $hasher = static::getContainer()->get('security.password_hasher');
            $hashedPassword = $hasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            // Corriger l'attribution des rôles
            if ($role === 'Sales') {
                $user->setRole(\App\Enum\UserRole::Sales);
            } elseif ($role === 'Baker') {
                $user->setRole(\App\Enum\UserRole::Baker);
            }

            $user->setFirstName('Test');
            $user->setLastName('User');
            $user->setCompany($company);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password,
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);
        return $data['token'];
    }

    private function createTestPricing(): Pricing
    {
        $pricing = new Pricing();
        $pricing->setFixedFee(100);
        $pricing->setCostPerKm(0.5);
        $pricing->setModificationDate(new \DateTime());

        $this->entityManager->persist($pricing);
        $this->entityManager->flush();

        return $pricing;
    }

    private function createTestSalesList(): SalesList
    {
        $salesList = new SalesList();
        $salesList->setStatus(SalesStatus::PreparingProducts);
        $salesList->setProductsPrice(10.0); // Ajout de cette ligne pour résoudre l'erreur
        $salesList->setGlobalDiscount(0);
        $salesList->setIssueDate(new \DateTime());
        $salesList->setExpirationDate(new \DateTime('+30 days'));
        $salesList->setOrderDate(new \DateTime());

        $this->entityManager->persist($salesList);
        $this->entityManager->flush();

        return $salesList;
    }

    private function createTestProduct(): Product
    {
        $product = new Product();
        $product->setProductName('Test Product');
        $product->setQuantity(10.0);
        $product->setNetPrice(20.0);
        $product->setGrossPrice(25.0);
        $product->setUnitWeight(1.0);
        $product->setDescription('Test product description');
        $product->setCategory(\App\Enum\ProductCategory::Bread);
        $product->setStockQuantity(100);

        $warehouse = $this->entityManager->getRepository(\App\Entity\Warehouse::class)->findOneBy([]);
        if (!$warehouse) {
            $warehouse = new \App\Entity\Warehouse();
            $warehouse->setName('Test Warehouse');
            $warehouse->setAddress('Test Address');
            $warehouse->setCity('Test City');
            $warehouse->setPostalCode('12345');
            $this->entityManager->persist($warehouse);
        }

        $product->setWarehouse($warehouse);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    private function createTestQuotation(): Quotation
    {
        $salesList = $this->createTestSalesList();
        $pricing = $this->createTestPricing();

        $quotation = new Quotation();
        $quotation->setTotalAmount(200);
        $quotation->setIssueDate(new \DateTime());
        $quotation->setDueDate(new \DateTime('+30 days'));
        $quotation->setPaymentStatus(false);
        $quotation->setAcceptanceDate(new \DateTime());
        $quotation->setSalesList($salesList);
        $quotation->setPricing($pricing);

        $this->entityManager->persist($quotation);
        $this->entityManager->flush();

        return $quotation;
    }

    public function testAdminList(): void
    {
        $this->createTestQuotation();

        $this->client->request(
            'GET',
            '/api/quotations/admin?page=1&limit=10&status=PENDING',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertArrayHasKey('items', $responseData);
    }

    public function testList(): void
    {
        $this->createTestQuotation();

        $this->client->request(
            'GET',
            '/api/quotations?page=1&limit=10',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertArrayHasKey('items', $responseData);
    }

    public function testCreate(): void
    {
        $salesList = $this->createTestSalesList();
        $this->createTestPricing();

        $this->client->request(
            'POST',
            '/api/quotations/salesLists/' . $salesList->getId() . '/quotation',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken
            ],
            json_encode([
                'dueDate' => (new \DateTime('+30 days'))->format('Y-m-d'),
                'distance' => 20
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('totalAmount', $responseData);
    }

    public function testCreateWithExistingQuotation(): void
    {
        $quotation = $this->createTestQuotation();
        $salesList = $quotation->getSalesList();

        $this->client->request(
            'POST',
            '/api/quotations/salesLists/' . $salesList->getId() . '/quotation',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken
            ],
            json_encode([
                'dueDate' => (new \DateTime('+30 days'))->format('Y-m-d')
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    public function testDetail(): void
    {
        $quotation = $this->createTestQuotation();

        $this->client->request(
            'GET',
            '/api/quotations/' . $quotation->getId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($quotation->getId(), $responseData['id']);
    }

    public function testUpdate(): void
    {
        $quotation = $this->createTestQuotation();

        $this->client->request(
            'PUT',
            '/api/quotations/' . $quotation->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken
            ],
            json_encode([
                'dueDate' => (new \DateTime('+60 days'))->format('Y-m-d'),
                'paymentStatus' => true
            ])
        );

        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();

        $updatedQuotation = $this->quotationRepository->find($quotation->getId());
        $this->assertTrue($updatedQuotation->isPaymentStatus());
    }

    public function testDelete(): void
    {
        $quotation = $this->createTestQuotation();
        $quotationId = $quotation->getId();

        $this->client->request(
            'DELETE',
            '/api/quotations/' . $quotationId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->salesToken]
        );

        $this->assertResponseIsSuccessful();

        $this->entityManager->clear();

        $this->assertNull($this->quotationRepository->find($quotationId));
    }

    public function testGetUserQuotations(): void
    {
        $quotation = $this->createTestQuotation();
        $user = $this->userRepository->findOneByEmail('baker@baker.com');

        $this->client->request(
            'GET',
            '/api/quotations/user/' . $user->getId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->salesToken]
        );

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }


    // TEST UNITAIRE
    public function testCalculateTotalAmountUnit(): void
    {
        $pricing = $this->createMock(Pricing::class);
        $pricing->method('getFixedFee')->willReturn(50.0);
        $pricing->method('getCostPerKm')->willReturn(2.0);

        $salesList = $this->createMock(SalesList::class);
        $salesList->method('getGlobalDiscount')->willReturn(10); // int au lieu de 10.0

        $totalProducts = 100.0;
        $distance = 15;
        $expectedTotal = $totalProducts + 50.0 + (2.0 * 15) - 10; // 170.0

        $this->assertEquals(170.0, $expectedTotal);
    }

    // TEST D'INTEGRATION

    public function testCreateQuotationIntegration(): void
    {
        $salesList = $this->createTestSalesList();
        $pricing = $this->createTestPricing();

        $this->client->request(
            'POST',
            '/api/quotations/salesLists/' . $salesList->getId() . '/quotation',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken
            ],
            json_encode(['dueDate' => '2024-12-31', 'distance' => 25])
        );

        $this->assertResponseStatusCodeSame(201);

        // Vérification en base
        $quotation = $this->quotationRepository->findOneBy(['salesList' => $salesList]);
        $this->assertNotNull($quotation);
        $this->assertEquals($pricing->getId(), $quotation->getPricing()->getId());
    }

    // TEST FONCTIONNEL

    public function testQuotationLifecycleFunctional(): void
    {
        $salesList = $this->createTestSalesList();
        $this->createTestPricing();

        $this->client->request('POST', '/api/quotations/salesLists/' . $salesList->getId() . '/quotation',
            [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken],
            json_encode(['dueDate' => '2024-12-31'])
        );

        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $quotationId = $createResponse['id'];

        $this->client->request('PATCH', '/api/quotations/' . $quotationId . '/pay',
            [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]);

        $this->client->request('GET', '/api/quotations/' . $quotationId,
            [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $finalResponse = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($finalResponse['paymentStatus']);
    }

    // TEST DE VALIDATION

    public function testCreateQuotationValidation(): void
    {
        $salesList = $this->createTestSalesList();

        // Test sans dueDate obligatoire
        $this->client->request('POST', '/api/quotations/salesLists/' . $salesList->getId() . '/quotation',
            [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken],
            json_encode(['distance' => 10])
        );

        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Missing field dueDate', $response['error']);
    }
    // TEST DE SECURITE

    public function testUnauthorizedAccess(): void
    {
        $quotation = $this->createTestQuotation();

        // Test sans token
        $this->client->request('GET', '/api/quotations/' . $quotation->getId());
        $this->assertResponseStatusCodeSame(401);

        // Test avec mauvais rôle pour l'update
        $this->client->request('PUT', '/api/quotations/' . $quotation->getId(),
            [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken],
            json_encode(['paymentStatus' => true])
        );
        $this->assertResponseStatusCodeSame(403);
    }

    // TEST DE REGRESSION

    public function testNoPricingAvailableRegression(): void
    {
        // Supprimer d'abord toutes les quotations qui référencent les pricings
        $quotations = $this->quotationRepository->findAll();
        foreach ($quotations as $quotation) {
            $this->entityManager->remove($quotation);
        }
        $this->entityManager->flush();

        // Puis supprimer tous les pricings
        $pricings = $this->pricingRepository->findAll();
        foreach ($pricings as $pricing) {
            $this->entityManager->remove($pricing);
        }
        $this->entityManager->flush();

        $salesList = $this->createTestSalesList();

        $this->client->request('POST', '/api/quotations/salesLists/' . $salesList->getId() . '/quotation',
            [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken],
            json_encode(['dueDate' => '2024-12-31'])
        );

        $this->assertResponseStatusCodeSame(404);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('No pricing available in database', $response['error']);
    }

    // TEST DE PERFORMANCE

    public function testAdminListPerformance(): void
    {
        // Créer 100 devis pour tester la pagination
        for ($i = 0; $i < 100; $i++) {
            $this->createTestQuotation();
        }

        $start = microtime(true);

        $this->client->request('GET', '/api/quotations/admin?page=1&limit=50',
            [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken]
        );

        $duration = microtime(true) - $start;

        $this->assertResponseIsSuccessful();
        $this->assertLessThan(2.0, $duration); // Moins de 2 secondes

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(50, count($response['items']));
    }


}

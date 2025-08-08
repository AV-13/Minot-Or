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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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

        // Création des tokens pour les tests
        $this->adminToken = $this->getToken('sales@sales.com', 'Sales', 'sales');
        $this->userToken = $this->getToken('baker@baker.com', 'Baker', 'baker');
        $this->salesToken = $this->getToken('sales@sales.com', 'Sales', 'sales');
    }

    private function getToken(string $email, string $role, string $password): string
    {
        // Création d'un utilisateur pour le test
        $user = $this->userRepository->findOneByEmail($email);
        if (!$user) {
            $user = new User();
            $user->setEmail($email);

            // Utiliser le service de hachage de Symfony pour créer un mot de passe compatible
            $hasher = static::getContainer()->get('security.password_hasher');
            $hashedPassword = $hasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $user->setRoles([$role]);
            $user->setFirstName('Test');
            $user->setLastName('User');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // Requête pour obtenir le token
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
        $product->setNetPrice(20.0);  // Ce champ est probablement mappé à 'products_price'
        $product->setGrossPrice(25.0);
        $product->setUnitWeight(1.0);
        $product->setDescription('Test product description');
        $product->setCategory(\App\Enum\ProductCategory::Bread);
        $product->setStockQuantity(100);

        // Créer ou récupérer un entrepôt pour le produit
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
        // Création de données de test
        $this->createTestQuotation();

        // Test avec filtres
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

        // Vider le cache de l'EntityManager
        $this->entityManager->clear();

        // Vérifier que les modifications ont été appliquées
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

        // Vider le cache de l'EntityManager
        $this->entityManager->clear();

        // Vérifier que le devis a été supprimé
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
}
<?php
// tests/DataFixtures/OrderFixtures.php
namespace App\Tests\DataFixtures;

use App\Entity\SalesList;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Company;
use App\Entity\Warehouse;
use App\Entity\Contains;
use App\Enum\SalesStatus;
use App\Enum\UserRole;
use App\Enum\ProductCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer une company avec les bons champs
        $company = new Company();
        $company->setCompanyName('Test Company');
        $company->setCompanySiret('12345678901234');
        $company->setCompanyContact('contact@testcompany.com');
        $company->setUnsold(false);
        $manager->persist($company);

        // Créer un warehouse avec les bons champs
        $warehouse = new Warehouse();
        $warehouse->setWarehouseAddress('456 Warehouse Ave, Lyon 69000');
        $warehouse->setStorageCapacity(1000);
        $manager->persist($warehouse);

        // Créer un utilisateur
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('$2y$13$...');  // Mot de passe haché
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setRole(UserRole::Baker);
        $user->setCompany($company);
        $manager->persist($user);

        // Créer des produits
        $product1 = new Product();
        $product1->setProductName('Pain Complet');
        $product1->setQuantity(5.0);
        $product1->setNetPrice(8.99);
        $product1->setGrossPrice(10.99);
        $product1->setUnitWeight(0.5);
        $product1->setDescription('Pain complet aux céréales');
        $product1->setCategory(ProductCategory::Bread);
        $product1->setStockQuantity(50);
        $product1->setWarehouse($warehouse);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setProductName('Farine T65');
        $product2->setQuantity(10.0);
        $product2->setNetPrice(12.99);
        $product2->setGrossPrice(15.99);
        $product2->setUnitWeight(1.0);
        $product2->setDescription('Farine de blé T65');
        $product2->setCategory(ProductCategory::Flour);
        $product2->setStockQuantity(100);
        $product2->setWarehouse($warehouse);
        $manager->persist($product2);

        // Créer une liste de vente (SalesList)
        $salesList = new SalesList();
        $salesList->setStatus(SalesStatus::Pending);
        $salesList->setProductsPrice(26.98); // Prix total des produits
        $salesList->setGlobalDiscount(5); // 5% de remise
        $salesList->setIssueDate(new \DateTime());
        $salesList->setExpirationDate(new \DateTime('+30 days'));
        $salesList->setOrderDate(new \DateTime());
        $manager->persist($salesList);

        // Créer les relations Contains pour lier produits et salesList
        $contains1 = new Contains();
        $contains1->setProduct($product1);
        $contains1->setSalesList($salesList);
        $contains1->setProductQuantity(2); // 2 pains complets
        $contains1->setProductDiscount(0); // Pas de remise sur ce produit
        $manager->persist($contains1);

        $contains2 = new Contains();
        $contains2->setProduct($product2);
        $contains2->setSalesList($salesList);
        $contains2->setProductQuantity(1); // 1 sac de farine
        $contains2->setProductDiscount(0); // Pas de remise sur ce produit
        $manager->persist($contains2);

        $manager->flush();
    }
}

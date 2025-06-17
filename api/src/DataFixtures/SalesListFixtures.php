<?php
namespace App\DataFixtures;

use App\Entity\SalesList;
use App\Enum\SalesStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SalesListFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
//        $status = $faker->randomElement(SalesStatus::cases());

        for ($i = 0; $i < 30; $i++) {
            $salesList = new SalesList();
            $salesList->setStatus($faker->randomElement(SalesStatus::cases()));
            $salesList->setProductsPrice($faker->randomFloat(2, 100, 2000));
            $salesList->setGlobalDiscount($faker->numberBetween(0, 20));
            $issueDate = $faker->dateTimeBetween('-2 months', 'now');
            $expirationDate = (clone $issueDate)->modify('+1 month');
            $salesList->setIssueDate($issueDate);
            $salesList->setExpirationDate($expirationDate);
            $salesList->setOrderDate($faker->dateTimeBetween($issueDate, $expirationDate));
            $manager->persist($salesList);

            $this->addReference('sales_list_' . $i, $salesList);
        }

        $manager->flush();
    }
}

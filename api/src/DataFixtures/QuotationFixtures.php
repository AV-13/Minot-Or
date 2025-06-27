<?php
namespace App\DataFixtures;

use App\Entity\Quotation;
use App\Entity\SalesList;
use App\Entity\Pricing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuotationFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            SalesListFixtures::class,
            PricingFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $salesLists = $manager->getRepository(SalesList::class)->findAll();
        $pricings = $manager->getRepository(Pricing::class)->findAll();

        foreach ($salesLists as $salesList) {
            $quotation = new Quotation();
            $quotation->setSalesList($salesList);
            $quotation->setTotalAmount($faker->randomFloat(2, 100, 2000));
            $quotation->setIssueDate($faker->dateTimeBetween('-2 months', 'now'));
            $quotation->setDueDate($faker->dateTimeBetween('now', '+1 month'));
            $quotation->setPaymentStatus($faker->boolean);
            $quotation->setAcceptanceDate($faker->dateTimeBetween('-2 months', 'now'));
            $quotation->setPricing($faker->randomElement($pricings));
            $manager->persist($quotation);
        }

        $manager->flush();
    }
}

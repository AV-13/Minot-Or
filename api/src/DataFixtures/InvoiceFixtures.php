<?php
namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\SalesList;
use App\Entity\Pricing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
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
            $invoice = new Invoice();
            $invoice->setSalesList($salesList);
            $invoice->setTotalAmount($faker->randomFloat(2, 100, 2000));
            $invoice->setIssueDate($faker->dateTimeBetween('-2 months', 'now'));
            $invoice->setDueDate($faker->dateTimeBetween('now', '+1 month'));
            $invoice->setPaymentStatus($faker->boolean);
            $invoice->setAcceptanceDate($faker->dateTimeBetween('-2 months', 'now'));
            $invoice->setPricing($faker->randomElement($pricings));
            $manager->persist($invoice);
        }

        $manager->flush();
    }
}

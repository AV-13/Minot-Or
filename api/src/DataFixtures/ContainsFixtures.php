<?php
namespace App\DataFixtures;

use App\Entity\Contains;
use App\Entity\Product;
use App\Entity\SalesList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContainsFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            SalesListFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $products = $manager->getRepository(Product::class)->findAll();
        $salesLists = $manager->getRepository(SalesList::class)->findAll();

        foreach ($salesLists as $salesList) {
            $numProducts = $faker->numberBetween(1, 5);
            $selectedProducts = $faker->randomElements($products, $numProducts);

            foreach ($selectedProducts as $product) {
                $contains = new Contains();
                $contains->setSalesList($salesList);
                $contains->setProduct($product);
                $contains->setProductQuantity($faker->numberBetween(1, 20));
                $contains->setProductDiscount($faker->numberBetween(0, 15));
                $manager->persist($contains);
            }
        }

        $manager->flush();
    }
}

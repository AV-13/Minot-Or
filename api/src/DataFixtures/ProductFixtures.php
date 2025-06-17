<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Warehouse;
use App\Enum\ProductCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = [
            ProductCategory::Flour,
            ProductCategory::Oil,
            ProductCategory::Egg,
            ProductCategory::Yeast,
            ProductCategory::Salt,
            ProductCategory::Sugar,
            ProductCategory::Butter,
            ProductCategory::Milk,
            ProductCategory::Seed,
            ProductCategory::Chocolate,
            ProductCategory::Bread
        ];

        $names = [
            'Farine T65', 'Farine complète', 'Farine de seigle', 'Levure boulangère',
            'Graines de tournesol', 'Graines de lin', 'Améliorant pain', 'Farine bio'
        ];

        // On suppose que les entrepôts sont référencés comme warehouse_0 à warehouse_40
        for ($w = 0; $w < 41; $w++) {
            $warehouse = $this->getReference('warehouse_' . $w, Warehouse::class);
            $nbProducts = $faker->numberBetween(4, 8);
            for ($i = 0; $i < $nbProducts; $i++) {
                $product = new Product();
                $product->setWarehouse($warehouse);
                $product->setProductName($faker->randomElement($names));
                $product->setQuantity($faker->randomFloat(2, 50, 500)); // quantité par lot
                $product->setNetPrice($faker->randomFloat(2, 10, 50));
                $product->setGrossPrice($faker->randomFloat(2, 12, 60));
                $product->setUnitWeight($faker->randomFloat(2, 0.5, 25));
                $product->setCategory($faker->randomElement($categories));
                $product->setStockQuantity($faker->numberBetween(20, 500));
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}

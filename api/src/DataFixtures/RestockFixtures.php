<?php
namespace App\DataFixtures;

use App\Entity\Restock;
use App\Entity\Supplier;
use App\Entity\Truck;
use App\Entity\Product;
use App\Enum\OrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RestockFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            SupplierFixtures::class,
            TruckFixtures::class,
            ProductFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $suppliers = $manager->getRepository(Supplier::class)->findAll();
        $trucks = $manager->getRepository(Truck::class)->findAll();
        $products = $manager->getRepository(Product::class)->findAll();
        $statuses = ['pending', 'in_progress', 'delivered', 'cancelled'];

        for ($i = 0; $i < 30; $i++) {
            $restock = new Restock();
            $supplier = $faker->randomElement($suppliers);
            $truck = $faker->randomElement($trucks);
            $product = $faker->randomElement($products);

            $restock->setSupplier($supplier);
            $restock->setTruck($truck);
            $restock->setProduct($product);
            $restock->setSupplierProductQuantity($faker->numberBetween(50, 500));
            $restock->setOrderNumber('CMD' . $faker->unique()->numerify('2024####-##'));
            $restock->setOrderDate($faker->dateTimeBetween('-2 months', 'now'));
            $restock->setOrderStatus($faker->randomElement(OrderStatus::cases()));

            $manager->persist($restock);
        }

        $manager->flush();
    }
}

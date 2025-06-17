<?php

namespace App\DataFixtures;

use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class WarehouseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 41; $i++) {
            $warehouse = new Warehouse();
            $warehouse->setWarehouseAddress($faker->streetAddress . ', ' . $faker->postcode . ' ' . $faker->city);
            $warehouse->setStorageCapacity($faker->numberBetween(500, 2000));
            $manager->persist($warehouse);
            $this->addReference('warehouse_' . $i, $warehouse);
        }

        $manager->flush();
    }
}

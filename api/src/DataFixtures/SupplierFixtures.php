<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SupplierFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $supplier = new Supplier();
            $supplier->setSupplierName('Minoterie ' . $faker->lastName);
            $supplier->setSupplierAddress($faker->address);
            $manager->persist($supplier);
            $this->addReference('supplier_' . $i, $supplier);
        }

        $manager->flush();
    }
}

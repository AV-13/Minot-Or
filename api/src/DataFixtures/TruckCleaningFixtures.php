<?php
namespace App\DataFixtures;

use App\Entity\TruckCleaning;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TruckCleaningFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {
            $cleaning = new TruckCleaning();
            $startDate = $faker->dateTimeBetween('-2 months', 'now');
            $endDate = (clone $startDate)->modify('+1 day');

            $cleaning->setCleaningStartDate($startDate);
            $cleaning->setCleaningEndDate($endDate);
            $cleaning->setObservations($faker->sentence(8));

            $manager->persist($cleaning);
        }

        $manager->flush();
    }
}

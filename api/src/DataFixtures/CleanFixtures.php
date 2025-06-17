<?php
namespace App\DataFixtures;

use App\Entity\Clean;
use App\Entity\Truck;
use App\Entity\TruckCleaning;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CleanFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            TruckFixtures::class,
            TruckCleaningFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $truckCleanings = $manager->getRepository(TruckCleaning::class)->findAll();
        $trucks = $manager->getRepository(Truck::class)->findAll();

        foreach ($truckCleanings as $cleaning) {
            $clean = new Clean();
            $clean->setTruckCleaning($cleaning);
            $clean->setTruck($trucks[array_rand($trucks)]);
            $manager->persist($clean);
        }

        $manager->flush();
    }
}

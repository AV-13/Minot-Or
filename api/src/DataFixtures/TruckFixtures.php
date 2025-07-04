<?php

namespace App\DataFixtures;

use App\Entity\Truck;
use App\Entity\Warehouse;
use App\Enum\TruckCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TruckFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void
    {
        $monocuveWarehouses = [0, 1, 7, 20, 31, 32, 34];

        for ($w = 0; $w < 41; $w++) {
            /** @var Warehouse $warehouse */
            $warehouse = $this->getReference('warehouse_' . $w, Warehouse::class);

            $truck1 = new Truck();
            $truck1->setWarehouse($warehouse);
            $truck1->setRegistrationNumber('TRK-' . $w . '-PP');
            $truck1->setTruckType(TruckCategory::PorteurPalettes);
            $truck1->setIsAvailable(true);
            $truck1->setDeliveryCount(0);
            $truck1->setTransportDistance(0);
            $truck1->setTransportFee(0);
            $manager->persist($truck1);

            if (in_array($w, $monocuveWarehouses, true)) {
                $truck2 = new Truck();
                $truck2->setWarehouse($warehouse);
                $truck2->setRegistrationNumber('TRK-' . $w . '-MC');
                $truck2->setTruckType(TruckCategory::Monocuve);
                $truck2->setIsAvailable(true);
                $truck2->setDeliveryCount(0);
                $truck2->setTransportDistance(0);
                $truck2->setTransportFee(0);
                $manager->persist($truck2);
            }

            if (random_int(1, 100) <= 20) {
                $truck3 = new Truck();
                $truck3->setWarehouse($warehouse);
                $truck3->setRegistrationNumber('TRK-' . $w . '-A');
                $truck3->setTruckType(TruckCategory::Autre);
                $truck3->setIsAvailable(true);
                $truck3->setDeliveryCount(0);
                $truck3->setTransportDistance(0);
                $truck3->setTransportFee(0);
                $manager->persist($truck3);
            }
        }

        $manager->flush();
    }
}

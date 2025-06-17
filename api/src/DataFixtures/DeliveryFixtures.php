<?php
namespace App\DataFixtures;

use App\Entity\Delivery;
use App\Entity\SalesList;
use App\Enum\DeliveryStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DeliveryFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            SalesListFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $salesLists = $manager->getRepository(SalesList::class)->findAll();
//        $statuses = ['in_progress', 'delivered', 'pending', 'cancelled'];

        foreach ($salesLists as $i => $salesList) {
            $delivery = new Delivery();
            $delivery->setSalesList($salesList);
            $delivery->setDeliveryDate($faker->dateTimeBetween('-1 month', '+1 month'));
            $delivery->setDeliveryAddress($faker->address);
            $delivery->setDeliveryNumber('DLV' . $faker->unique()->numerify('2024####'));
            $delivery->setDeliveryStatus($faker->randomElement(DeliveryStatus::cases()));
            $delivery->setDriverRemark($faker->optional()->sentence(6));
            $delivery->setQrCode('QR-' . $faker->unique()->md5);

            $manager->persist($delivery);
        }

        $manager->flush();
    }
}

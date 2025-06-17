<?php
namespace App\DataFixtures;

use App\Entity\Pricing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PricingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Tarifs par défaut selon le cahier des charges
        $defaultPricings = [
            // Transport monocuve (vrac)
            ['fixed_fee' => 175, 'cost_per_km' => 2.5],
            // Transport palettes minoterie → boulangerie
            ['fixed_fee' => 75, 'cost_per_km' => 1.25],
            // Transport palettes entrepôt → boulangerie (base 50€, coût préparation 0.05€/kg, ici on ne gère que le forfait)
            ['fixed_fee' => 50, 'cost_per_km' => 1.25],
        ];

        foreach ($defaultPricings as $pricingData) {
            $pricing = new Pricing();
            $pricing->setFixedFee($pricingData['fixed_fee']);
            $pricing->setCostPerKm($pricingData['cost_per_km']);
            $pricing->setModificationDate($faker->dateTimeBetween('-2 months', 'now'));
            $manager->persist($pricing);
        }

        // Ajout de quelques tarifs aléatoires pour la diversité
        for ($i = 0; $i < 7; $i++) {
            $pricing = new Pricing();
            $pricing->setFixedFee($faker->randomFloat(2, 40, 200));
            $pricing->setCostPerKm($faker->randomFloat(2, 1, 3));
            $pricing->setModificationDate($faker->dateTimeBetween('-2 months', 'now'));
            $manager->persist($pricing);
        }

        $manager->flush();
    }
}

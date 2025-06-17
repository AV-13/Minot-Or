<?php
namespace App\DataFixtures;

use App\Entity\Evaluate;
use App\Entity\SalesList;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EvaluateFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            SalesListFixtures::class,
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $salesLists = $manager->getRepository(SalesList::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($salesLists as $salesList) {
            // On choisit 1 à 2 reviewers aléatoires pour chaque salesList
            $reviewers = $faker->randomElements($users, $faker->numberBetween(1, 2));
            foreach ($reviewers as $reviewer) {
                $evaluate = new Evaluate();
                $evaluate->setSalesList($salesList);
                $evaluate->setReviewer($reviewer);
                $evaluate->setQuoteAccepted($faker->boolean(70)); // 70% de chances d'être accepté
                $manager->persist($evaluate);
            }
        }

        $manager->flush();
    }
}

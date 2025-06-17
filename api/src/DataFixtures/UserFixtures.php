<?php
namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Employés Minot'Or (beaucoup, avec tous les rôles)
        $minotor = $this->getReference(CompanyFixtures::MINOTOR_REF, Company::class);
        $roles = [
            UserRole::Sales,
            UserRole::OrderPreparer,
            UserRole::Procurement,
            UserRole::Driver,
            UserRole::Baker, // Pour la diversité, même si peu probable
        ];
        // Générer beaucoup d'employés Minot'Or
        for ($i = 0; $i < 30; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->unique()->email);
            $user->setPassword('password'); // À encoder selon tes besoins
            $role = $faker->randomElement(UserRole::cases());
            $user->setRole($role);
            $user->setCompany($minotor);
            $manager->persist($user);
        }

        // Employés des autres entreprises (rôle Baker uniquement)
        for ($i = 0; $i < CompanyFixtures::COMPANY_COUNT; $i++) {
            $company = $this->getReference('company_' . $i, Company::class);
            for ($j = 0; $j < 8; $j++) {
                $user = new User();
                $user->setFirstName($faker->firstName);
                $user->setLastName($faker->lastName);
                $user->setEmail($faker->unique()->email);
                $user->setPassword('password');
                $user->setRole(UserRole::Baker);
                $user->setCompany($company);
                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}

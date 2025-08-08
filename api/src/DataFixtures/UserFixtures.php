<?php
namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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

            // Hacher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $role = $faker->randomElement(UserRole::cases());
            $user->setRole($role);
            $user->setRoles([$role->toSymfonyRole()]);
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

                // Hacher le mot de passe
                $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
                $user->setPassword($hashedPassword);

                $user->setRole(UserRole::Baker);
                $user->setRoles([UserRole::Baker->toSymfonyRole()]);
                $user->setCompany($company);
                $manager->persist($user);
            }
        }

        // Création des utilisateurs spécifiques pour les tests
        $user = new User();
        $user->setFirstName('sales');
        $user->setLastName('sales');
        $user->setEmail('sales@sales.com');

        // Hacher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'sales');
        $user->setPassword($hashedPassword);

        $user->setRole(UserRole::Sales);
        $user->setRoles([UserRole::Sales->toSymfonyRole()]);
        $user->setCompany($company);
        $manager->persist($user);

        $user = new User();
        $user->setFirstName('baker');
        $user->setLastName('baker');
        $user->setEmail('baker@baker.com');

        // Hacher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'baker');
        $user->setPassword($hashedPassword);

        $user->setRole(UserRole::Baker);
        $user->setRoles([UserRole::Baker->toSymfonyRole()]);
        $user->setCompany($company);
        $manager->persist($user);

        $manager->flush();
    }
}
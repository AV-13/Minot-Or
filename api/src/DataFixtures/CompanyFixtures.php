<?php
namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyFixtures extends Fixture
{
    public const MINOTOR_REF = 'company_minotor';
    public const COMPANY_COUNT = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Entreprise Minot'Or
        $minotor = new Company();
        $minotor->setCompanyName('Minot\'Or');
        $minotor->setCompanySiret('12345678901234');
        $minotor->setCompanyContact('contact@minotor.fr');
        $manager->persist($minotor);
        $this->addReference(self::MINOTOR_REF, $minotor);

        // Autres entreprises
        for ($i = 0; $i < self::COMPANY_COUNT; $i++) {
            $company = new Company();
            $company->setCompanyName($faker->company);
            $company->setCompanySiret($faker->unique()->numerify('##############'));
            $company->setCompanyContact($faker->companyEmail);
            $manager->persist($company);
            $this->addReference('company_' . $i, $company);
        }

        $manager->flush();
    }
}

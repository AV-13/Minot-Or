<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Warehouse;
use App\Enum\ProductCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = [
            ProductCategory::Flour,
            ProductCategory::Oil,
            ProductCategory::Egg,
            ProductCategory::Yeast,
            ProductCategory::Salt,
            ProductCategory::Sugar,
            ProductCategory::Butter,
            ProductCategory::Milk,
            ProductCategory::Seed,
            ProductCategory::Chocolate,
            ProductCategory::Bread
        ];

        $names = [
            'Farine T65', 'Farine complète', 'Farine de seigle', 'Levure boulangère',
            'Graines de tournesol', 'Graines de lin', 'Améliorant pain', 'Farine bio'
        ];

        $descriptions = [
            "Farine de blé T55 idéale pour la pâtisserie et la viennoiserie. Texture fine et blanche, parfaite pour les pâtes légères.",
            "Semoule de blé dur extra-fine destinée à la fabrication de couscous et de pâtes artisanales.",
            "Farine complète T150 issue de blés français, riche en fibres, pour pains rustiques et recettes santé.",
            "Farine de maïs sans gluten, idéale pour les galettes, tortillas et polenta.",
            "Mélange spécial pain de campagne avec graines (tournesol, lin, sésame) prêt à l’emploi.",
            "Farine de seigle T130 pour la fabrication de pains noirs et de spécialités nordiques.",
            "Bran de blé (son) utilisé comme complément alimentaire pour animaux ou dans l’agriculture biologique.",
            "Farine bio T80 issue de l’agriculture biologique, pour pains traditionnels et baguettes.",
            "Grains de blé dur nettoyés et calibrés, prêts pour la mouture ou l’exportation.",
            "Farine de riz blanche extra-fine adaptée aux régimes sans gluten et à la cuisine asiatique.",
            "Mélange tout usage pour pain et pizza, riche en gluten, assurant une bonne levée.",
            "Sous-produit de mouture : remoulage de blé utilisé dans l’alimentation animale.",
            "Farine de châtaigne artisanale, au goût légèrement sucré, parfaite pour crêpes et gâteaux.",
            "Farine spéciale pour machine à pain avec levure incorporée.",
            "Pépins et enveloppes de blé conditionnés pour compost ou litière bio.",
        ];


        // On suppose que les entrepôts sont référencés comme warehouse_0 à warehouse_40
        for ($w = 0; $w < 41; $w++) {
            $warehouse = $this->getReference('warehouse_' . $w, Warehouse::class);
            $nbProducts = $faker->numberBetween(4, 8);
            for ($i = 0; $i < $nbProducts; $i++) {
                $product = new Product();
                $product->setWarehouse($warehouse);
                $product->setProductName($faker->randomElement($names));
                $product->setDescription($faker->randomElement($descriptions));
                $product->setQuantity($faker->randomFloat(2, 50, 500)); // quantité par lot
                $product->setNetPrice($faker->randomFloat(2, 10, 50));
                $product->setGrossPrice($faker->randomFloat(2, 12, 60));
                $product->setUnitWeight($faker->randomFloat(2, 0.5, 25));
                $product->setCategory($faker->randomElement($categories));
                $product->setStockQuantity($faker->numberBetween(20, 500));
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}

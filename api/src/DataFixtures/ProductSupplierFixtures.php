<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\ProductSupplier;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductSupplierFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            SupplierFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $products = $manager->getRepository(Product::class)->findAll();
        $suppliers = $manager->getRepository(Supplier::class)->findAll();

        // Chaque fournisseur peut proposer plusieurs produits
        foreach ($suppliers as $supplier) {
            // On sélectionne un nombre aléatoire de produits pour ce fournisseur
            $numProducts = $faker->numberBetween(2, 10);
            $randomProducts = $faker->randomElements($products, $numProducts);

            foreach ($randomProducts as $product) {
                // On crée la relation ProductSupplier
                $productSupplier = new ProductSupplier();
                $productSupplier->setProduct($product);
                $productSupplier->setSupplier($supplier);
                // On ajoute éventuellement d'autres propriétés selon l'entité
                // Par exemple, priorité du fournisseur, délai de livraison, etc.

                $manager->persist($productSupplier);

                // On établit aussi la relation ManyToMany
                $supplier->addProduct($product);
            }
        }

        $manager->flush();
    }
}

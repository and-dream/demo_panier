<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
       $this->faker = Factory::create('fr_FR');      
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        //une boucle si je veux plusieurs produits d'un seul coup
        for($i = 1; $i <= mt_rand(12,25); $i++)
        {
            $produit = new Product;

            //remplir le produit : on va set les propriétés
            $produit->setTitle($this->faker->sentence(2))
                    ->setImage($this->faker->imageUrl(640, 480, 'produit'))
                    ->setPrice($this->faker->randomFloat(2, 15, 900));

            $manager->persist($produit);
        }

        $manager->flush();
    }
}

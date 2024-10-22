<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create(); // Create a Faker instance

        // Create 10 sample products
        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($faker->word());
            $product->setPrice($faker->randomFloat(2, 1, 100)); // Random price between 1 and 100
            $product->setStockQuantity($faker->numberBetween(1, 100)); // Random stock quantity
            $product->setDescription($faker->sentence()); // Random description
            $product->setCreatedDatetime($faker->dateTimeThisYear()); // Random created datetime

            $manager->persist($product); // Persist the product entity
        }

        $manager->flush(); // Save all entities to the database
    }
}
<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=0; $i < 10; $i++) { 
            $category = new Category();
            $category->setName($faker->paragraph($nbSentences = 1, $variableNbSentences = true));

            $manager->persist($category);
            $manager->flush();

            $this->addReference('cat'.$i, $category);
        }

    }
}

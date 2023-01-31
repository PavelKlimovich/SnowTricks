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

        $categories = [
            'La manière de rideur',
            'Les grabs',
            'Les rotations',
            'Les flips',
            'Les rotations désaxées',
            'Les slides',
            'Les one foot tricks',
            'Old school',
            'New school',
            'Shred',
        ];

        for ($i=0; $i < 10; $i++) { 
            $category = new Category();
            $category->setName($categories[$i]);

            $manager->persist($category);
            $manager->flush();

            $this->addReference('cat'.$i, $category);
        }

    }
}

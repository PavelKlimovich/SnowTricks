<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 10; $i++) { 
            $image = new Image();
            $image->setUrl("http://placehold.it/350x150")
                ->setTrick($this->getReference('trick'.$i))
                ->setCreatedAt(new \DateTime());

            $manager->persist($image);
            $manager->flush();
        }

    }

    public function getDependencies()
    {
        return array(
            TrickFixture::class,
        );
    }
}

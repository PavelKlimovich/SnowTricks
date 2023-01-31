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
        $images = [
            '/tricks/1.jpg',
            '/tricks/2.jpg',
            '/tricks/3.jpg',
            '/tricks/4.jpg',
            '/tricks/5.jpg',
            '/tricks/6.jpg',
            '/tricks/7.jpg',
            '/tricks/8.jpg',
            '/tricks/9.jpg',
            '/tricks/10.jpg',
        ];

        for ($i=0; $i < 10; $i++) { 
            $image = new Image();
            $image->setUrl($images[$i])
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

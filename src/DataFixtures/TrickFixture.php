<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Trick;
use App\Services\HelperStringService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $trickList = ["OLLIE","FRONT ROLL","INDY","NOSE GRAB","TAIL PRESS","FRONTSIDE 180","BUTTER","TRIPOD","BACK FLIP","NOLLIE"];
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
            $trick = new Trick();

            $trick->setName($trickList[$i])
                ->setImage($images[$i])
                ->setContent($faker->text($maxNbChars = 200))
                ->setSlug(HelperStringService::slugify($trickList[$i]))
                ->setCategory($this->getReference('cat'.$i))
                ->setUser($this->getReference('user'))
                ->setCreatedAt(new \DateTime());

            $manager->persist($trick);
            $manager->flush();

            $this->addReference('trick'.$i, $trick);
        }
    }

    public function getDependencies()
    {
        return array(
            CategoryFixture::class,
            UserFixture::class,
        );
    }
}

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

        for ($i=0; $i < 10; $i++) { 
            $trick = new Trick();

            $trick->setName($trickList[$i])
                ->setImage("http://placehold.it/350x150")
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

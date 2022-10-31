<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 10; $i++) {
            $video = new Video();
            $video->setUrl("https://www.youtube.com/watch?v=V9xuy-rVj9w")
                ->setTrick($this->getReference('trick'.$i))
                ->setCreatedAt(new \DateTime());

            $manager->persist($video);
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
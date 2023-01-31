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
        $videos = [
            'https://www.youtube.com/embed/8KotvBY28Mo',
            'https://www.youtube.com/embed/hih9jIzOoRg',
            'https://www.youtube.com/embed/hih9jIzOoRg',
            'https://www.youtube.com/embed/CA5bURVJ5zk',
            'https://www.youtube.com/embed/CA5bURVJ5zk',
            'https://www.youtube.com/embed/mfNA0UEJo1Y',
            'https://www.youtube.com/embed/mfNA0UEJo1Y',
            'https://www.youtube.com/embed/qsd8uaex-Is',
            'https://www.youtube.com/embed/qsd8uaex-Is',
            'https://www.youtube.com/embed/V9xuy-rVj9w'
        ];

        for ($i=0; $i < 10; $i++) {
            $video = new Video();
            $video->setUrl($videos[$i])
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
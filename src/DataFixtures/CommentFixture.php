<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        for ($i=0; $i < 10; $i++) { 
            $comment = new Comment();
            $comment->setContent($faker->text($maxNbChars = 200))
                ->setTrick($this->getReference('trick'.$i))
                ->setUser($this->getReference('user'))
                ->setCreatedAt(new \DateTime());

            $manager->persist($comment);
            $manager->flush();
        }
        
    }

    public function getDependencies()
    {
        return array(
            TrickFixture::class,
            UserFixture::class,
        );
    }
}

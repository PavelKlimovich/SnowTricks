<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{ 
    public function load(ObjectManager $manager): void
    {
        $this->addUser($manager);
        $this->addCategory($manager);
        $this->addTrick($manager);
        $this->addComment($manager);
    }

    public function addUser(?ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("pklim")
            ->setEmail("pavel@kernl.fr")
            ->setPassword("password")
            ->setVerified(1)
            ->setCreatedAt(new \DateTime());
            
        $this->user = $user;
        $manager->persist($user);

        $manager->flush();
    }

    public function addCategory(ObjectManager $manager): void
    {
        $this->categories = [];
        $faker = Factory::create();

        for ($i=0; $i < 10; $i++) { 
            $category = new Category();
            $category->setName($faker->paragraph($nbSentences = 1, $variableNbSentences = true));

            $this->categories[] = $category;
            $manager->persist($category);
        }

        $manager->flush();
    }

    public function addTrick(ObjectManager $manager): void
    {
        $this->tricks = [];
        $faker = Factory::create();
        $trickList = ["OLLIE","FRONT ROLL","INDY","NOSE GRAB","TAIL PRESS","FRONTSIDE 180","BUTTER","TRIPOD","BACK FLIP","NOLLIE"];

        for ($i=0; $i < 10; $i++) { 
            $trick = new Trick();
            $key = array_rand($this->categories);
            $trick->setName($trickList[$i])
                ->setImage("http://placehold.it/350x150")
                ->setContent($faker->text($maxNbChars = 200))
                ->setCategory($this->categories[$key])
                ->setUser($this->user)
                ->setCreatedAt(new \DateTime());

            $this->tricks[] = $trick;
            $manager->persist($trick);
        }
        
        $manager->flush();
    }

    public function addComment(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        for ($i=0; $i < 10; $i++) { 
            $comment = new Comment();
            $key = array_rand($this->tricks);
            $comment->setContent($faker->text($maxNbChars = 200))
                ->setTrick($this->tricks[$key])
                ->setUser($this->user)
                ->setCreatedAt(new \DateTime());

            $manager->persist($comment);
        }
        
        $manager->flush();
    }
}

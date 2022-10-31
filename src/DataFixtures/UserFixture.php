<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("pklim")
            ->setEmail("pavel@kernl.fr")
            ->setPassword("password")
            ->setVerified(1)
            ->setCreatedAt(new \DateTime());
            
        $manager->persist($user);
        $manager->flush();
        $this->addReference('user', $user);
    }
}

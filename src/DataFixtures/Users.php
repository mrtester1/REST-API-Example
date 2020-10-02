<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Users as TrueUsers;

class Users extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new TrueUsers();
        $user->setEmail('user@test.com');
        $user->setUserGroup('90617e56-1220-40ee-95e9-1d9c8cf77d1b');
        $user->setActive(true);
        $manager->persist($user);

        $user = new TrueUsers();
        $user->setEmail('guest@test.com');
        $user->setUserGroup('d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9');
        $user->setActive(false);
        $manager->persist($user);

        $user = new TrueUsers();
        $user->setEmail('manager@test.com');
        $user->setUserGroup('8cd8e1b8-c9e3-4206-9402-29f854c398b7');
        $user->setActive(true);
        $manager->persist($user);

        $manager->flush();
    }
}

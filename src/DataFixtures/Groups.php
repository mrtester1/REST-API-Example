<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Groups as TrueGroups;

class Groups extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $group = new TrueGroups();
        $group->setId('d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9');
        $group->setName('guest');
        $manager->persist($group);

        $group = new TrueGroups();
        $group->setId('90617e56-1220-40ee-95e9-1d9c8cf77d1b');
        $group->setName('user');
        $manager->persist($group);

        $group = new TrueGroups();
        $group->setId('8cd8e1b8-c9e3-4206-9402-29f854c398b7');
        $group->setName('manager');
        $manager->persist($group);

        $manager->flush();
    }
}

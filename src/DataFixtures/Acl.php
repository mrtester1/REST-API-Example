<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Acl as TrueAcl;

class Acl extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $acl = new TrueAcl();
        $acl->setGroupId('d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9');
        $acl->setUrl('/api/v1/group_list');
        $acl->setMethod('GET');
        $manager->persist($acl);

        $acl = new TrueAcl();
        $acl->setGroupId('90617e56-1220-40ee-95e9-1d9c8cf77d1b');
        $acl->setUrl('/api/v1/group_list');
        $acl->setMethod('GET');
        $manager->persist($acl);

        $acl = new TrueAcl();
        $acl->setGroupId('8cd8e1b8-c9e3-4206-9402-29f854c398b7');
        $acl->setUrl('/api/v1/group_list');
        $acl->setMethod('GET');
        $manager->persist($acl);

        $acl = new TrueAcl();
        $acl->setGroupId('90617e56-1220-40ee-95e9-1d9c8cf77d1b');
        $acl->setUrl('/api/v1/user_get-by-id');
        $acl->setMethod('GET');
        $manager->persist($acl);

        $acl = new TrueAcl();
        $acl->setGroupId('8cd8e1b8-c9e3-4206-9402-29f854c398b7');
        $acl->setUrl('/api/v1/user_get-by-id');
        $acl->setMethod('GET');
        $manager->persist($acl);

        $acl = new TrueAcl();
        $acl->setGroupId('8cd8e1b8-c9e3-4206-9402-29f854c398b7');
        $acl->setUrl('/api/v1/user_update');
        $acl->setMethod('POST');
        $manager->persist($acl);

        $acl = new TrueAcl();
        $acl->setGroupId('8cd8e1b8-c9e3-4206-9402-29f854c398b7');
        $acl->setUrl('/api/v1/user_add');
        $acl->setMethod('POST');
        $manager->persist($acl);

        $manager->flush();
    }
}

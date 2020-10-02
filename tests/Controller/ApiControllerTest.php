<?php
namespace App\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Users;

class ApiControllerTest extends WebTestCase
{
    public function testShowGroupsForGuestNoHeaders()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/group_list', [], [], []);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowGroupsForGuestGroup()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/group_list', [], [], ['HTTP_X-User-Group' => 'd18b29bd-b4ef-4891-98d3-aa25ccc6e9a9']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowGroupsForManagerGroup()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/group_list', [], [], ['HTTP_X-User-Group' => '8cd8e1b8-c9e3-4206-9402-29f854c398b7']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowGroupsForUserGroup()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/group_list', [], [], ['HTTP_X-User-Group' => '90617e56-1220-40ee-95e9-1d9c8cf77d1b']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowGroupsForUnknownGroup()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/group_list', [], [], ['HTTP_X-User-Group' => '90617e56-1220-40ee-95e9-1d9c8cf77d1f']);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testShowGroupsForInvalidUuidAsToken()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/group_list', [], [], ['HTTP_X-User-Group' => 'invalid uuid']);
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    public function testGetUserWithInvalidUuidToken()
    {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => 'user@test.com']);
        $client->request('GET', '/api/v1/user_get-by-id/'.$user->getId(), [], [], ['HTTP_X-User-Group' => 'invalid uuid']);
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    public function testGetUserWithInvalidGroup()
    {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => 'user@test.com']);
        $client->request('GET', '/api/v1/user_get-by-id/'.$user->getId(), [], [], ['HTTP_X-User-Group' => '90617e56-1220-40ee-95e9-1d9c8cf77d1f']);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testGetUserWithValidGroupWithGuestGroupNoAccess()
    {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => 'user@test.com']);
        $client->request('GET', '/api/v1/user_get-by-id/'.$user->getId(), [], [], ['HTTP_X-User-Group' => 'd18b29bd-b4ef-4891-98d3-aa25ccc6e9a9']);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testGetUserWithValidGroupWithManagerGroup()
    {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => 'user@test.com']);
        $client->request('GET', '/api/v1/user_get-by-id/'.$user->getId(), [], [], ['HTTP_X-User-Group' => '8cd8e1b8-c9e3-4206-9402-29f854c398b7']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetUserWithValidGroupWithUserGroup()
    {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => 'user@test.com']);
        $client->request('GET', '/api/v1/user_get-by-id/'.$user->getId(), [], [], ['HTTP_X-User-Group' => '90617e56-1220-40ee-95e9-1d9c8cf77d1b']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateUserManagerAllowed() {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $client->request('POST', '/api/v1/user_add',
                [],
                [],
                ['HTTP_X-User-Group' => '8cd8e1b8-c9e3-4206-9402-29f854c398b7'],
                '{"email":"ivan.petrov@anymail.com","group":"d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9","firstName":"Ivan","lastName":"Petrov","gender":"M","dob":"09.07.1986","active":true}');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => "ivan.petrov@anymail.com"]);

        if ($user) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function testCreateUserGuestNotAllowed() {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $client->request('POST', '/api/v1/user_add',
                [],
                [],
                ['HTTP_X-User-Group' => 'd18b29bd-b4ef-4891-98d3-aa25ccc6e9a9'],
                '{"email":"ivan.petrov@anymail.com","group":"d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9","firstName":"Ivan","lastName":"Petrov","gender":"M","dob":"09.07.1986","active":true}');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => "ivan.petrov@anymail.com"]);

        if ($user) {
            $this->assertTrue(false);
        } else {
            $this->assertTrue(True);
        }
    }

    public function testCreateUserUserNotAllowed() {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $client->request('POST', '/api/v1/user_add',
                [],
                [],
                ['HTTP_X-User-Group' => '90617e56-1220-40ee-95e9-1d9c8cf77d1b'],
                '{"email":"ivan.petrov@anymail.com","group":"d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9","firstName":"Ivan","lastName":"Petrov","gender":"M","dob":"09.07.1986","active":true}');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => "ivan.petrov@anymail.com"]);

        if ($user) {
            $this->assertTrue(false);
        } else {
            $this->assertTrue(True);
        }
    }


    public function testUpdateUserManagerAllowed() {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => "user@test.com"]);

        $client->request('POST', '/api/v1/user_update',
                [],
                [],
                ['HTTP_X-User-Group' => '8cd8e1b8-c9e3-4206-9402-29f854c398b7'],
                '{"id":"'.$user->getId().'","email":"ivan.petrov@anymail.com","group":"d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9","firstName":"Ivan","lastName":"Petrov","gender":"M","dob":"09.07.1986","active":true}');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateUserGuestNotAllowed() {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => "user@test.com"]);

        $client->request('POST', '/api/v1/user_update',
                [],
                [],
                ['HTTP_X-User-Group' => 'd18b29bd-b4ef-4891-98d3-aa25ccc6e9a9'],
                '{"id":"'.$user->getId().'","email":"ivan.petrov@anymail.com","group":"d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9","firstName":"Ivan","lastName":"Petrov","gender":"M","dob":"09.07.1986","active":true}');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testUpdateUserUserNotAllowed() {
        $client = static::createClient();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;

        $user = self::$container
            ->get('doctrine')
            ->getRepository(Users::class)
            ->findOneBy(['email' => "user@test.com"]);

        $client->request('POST', '/api/v1/user_update',
                [],
                [],
                ['HTTP_X-User-Group' => '90617e56-1220-40ee-95e9-1d9c8cf77d1b'],
                '{"id":"'.$user->getId().'","email":"ivan.petrov@anymail.com","group":"d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9","firstName":"Ivan","lastName":"Petrov","gender":"M","dob":"09.07.1986","active":true}');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}
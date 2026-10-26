<?php

namespace App\Tests\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class SecurityControllerTest extends WebTestCase
{
    public function testResponse(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws Exception
     */
    public function testVisitingWhileLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // test the profile page
        $client->request('GET', '/profile');
        $this->assertResponseIsSuccessful();
    }
}

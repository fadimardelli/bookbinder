<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    /**
     * @throws Exception
     */
    public function testResponse(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $client->request('GET', '/book/1');
        $this->assertResponseIsSuccessful();
    }
}

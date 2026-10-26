<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchResultsControllerTest extends WebTestCase
{
    /**
     * @throws Exception
     */
    public function testIndex()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // mimic the form submission
        $crawler = $client->request('POST', '/searchresults', ['input' => 'austen: a']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // assert that there are X number of books in the results
        $this->assertCount(1, $crawler->filter('.col-md-3'));

        // assert that a book with a specific title is in the results
        $this->assertStringContainsString('The Complete Project Gutenberg Works of Jane Austen: A Linked Index of all PG Editions of Jane Austen', $client->getResponse()->getContent());
    }

    /**
     * @throws Exception
     */
    public function testIndexWithAuthorName()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // mimic the form submission
        $crawler = $client->request('POST', '/searchresults', ['input' => 'edgar allan']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // assert that there are X number of books in the results
        $this->assertCount(8, $crawler->filter('.col-md-3'));

        // assert that a book with a specific title is in the results
        $this->assertStringContainsString('Fall of the House of', $client->getResponse()->getContent());
    }
}
<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Attendee;
use App\Entity\Library;
use App\Entity\Book;
use App\Entity\Meeting;
use DateTime;

class ProfileControllerTest extends WebTestCase
{
    public function testIndexPostAttend()
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $testUser = $userRepository->findBy(['username'=>'opc']);

        // simulate $testUser being logged in
        $client->loginUser($testUser[0]);

        $attendee = new Attendee();
        $attendee->setUser($testUser[0]);

        $meetRepo = $entityManager->getRepository(Meeting::class);
        $meeting = $meetRepo->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $attendee->setMeeting($meeting);
        $entityManager->persist($attendee);
        $entityManager->flush();

        $attendRepository = $entityManager->getRepository(Attendee::class);
        $query = $attendRepository->createQueryBuilder('a')
            ->innerJoin('a.user', 'u')
            ->where('u.username LIKE :username')
            ->setParameter('username', '%' . $testUser[0]->getUsername() . '%')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $removed = $query->getSingleResult();

        $client->request('POST', '/profile', ['attend_id' => $removed->getMeeting()->getId()]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->getContainer()->get('doctrine')->getManager();
        $removed = $attendRepository->findBy(
            ['meeting'=> $removed->getMeeting()->getId(),
                'user'=> $testUser[0]->getId()]
        );

        $this->assertEmpty($removed);
    }

    /**
     * @throws Exception
     */
    public function testIndexPostHost()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $meeting = new Meeting();

        $libRepository = $entityManager->getRepository(Library::class);
        $location = $libRepository->find('1');
        $meeting->setLocation($location);

        $date = new DateTime('2023-08-17 12:52:33');
        $meeting->setDate($date);

        $bookRepository = $entityManager->getRepository(Book::class);
        $book = $bookRepository->find('1');
        $meeting->setBook($book);

        $userRepository = $entityManager->getRepository(User::class);
        $user= $userRepository->find('1');
        $meeting->setUser($user);

        $meeting->setDescription('new on3s');

        $entityManager->persist($meeting);
        $entityManager->flush();

        $meetRepository = $entityManager->getRepository(Meeting::class);
        $query = $meetRepository->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $removed = $query->getSingleResult();

        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findBy(['username'=>$removed->getUser()->getUsername()]);

        // simulate $testUser being logged in
        $client->loginUser($testUser[0]);

        $countBefore = count($meetRepository->findAll());

        $client->request('POST', '/profile', ['host_id' => $removed->getId()]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $countAfter = count($meetRepository->findAll());
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
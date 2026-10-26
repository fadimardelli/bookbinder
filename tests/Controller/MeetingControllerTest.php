<?php

namespace App\Tests\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Attendee;
use App\Entity\Meeting;
use App\Entity\User;

class MeetingControllerTest extends WebTestCase
{
    /**
     * @throws Exception
     */
    public function testJoinMeeting()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class);
        $testUser = $userRepository->findBy(['username'=>'Tester']);

        // simulate $testUser being logged in
        $client->loginUser($testUser[0]);

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $meetRepository = $entityManager->getRepository(Meeting::class);
        $query = $meetRepository->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $meeting = $query->getSingleResult();

        // mimic the form submission
        $client->request(
            'POST',
            '/meeting',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['id' => $meeting->getId()])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $repository = $entityManager->getRepository(Attendee::class);
        $query = $repository->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $added = $query->getSingleResult();

        $this->assertEquals($meeting->getId(), $added->getMeeting()->getId());

        $entityManager->remove($added);
        $entityManager->flush();
    }

    public function testFilter()
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $testUser = $userRepository->findBy(['username'=>'opc']);

        // simulate $testUser being logged in
        $client->loginUser($testUser[0]);

        $meetRepo = $entityManager->getRepository(Meeting::class);
        $meetings = $meetRepo->createQueryBuilder('m')
            ->innerJoin('m.book', 'b')
            ->innerJoin('m.location', 'l')
            ->innerJoin('m.user', 'u')
            ->where('b.title LIKE :input')
            ->orWhere('l.name LIKE :input')
            ->orWhere('u.username LIKE :input')
            ->setParameter('input', '%' . 'hoege' . '%')
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();

        $attendRepo = $entityManager->getRepository(Attendee::class);
        $attendees = $attendRepo->findBy(
            ['user' => $testUser[0]],
        );

        $attendedMeetingIds = array_map(function($attended) {
            return $attended->getMeeting()->getId();
        }, $attendees);

        $filteredMeetings = array_filter($meetings, function ($meeting) use ($attendedMeetingIds) {
            return !in_array($meeting->getId(), $attendedMeetingIds);
        });
        $count = count($filteredMeetings);

        // mimic the form submission
        $crawler = $client->request('POST', '/filtermeeting', ['meeting_input' => 'hoege']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // assert that there are X number of meetings in the results
        $this->assertCount($count, $crawler->filter('.mt-5'));

        // assert that a book with a specific description is in the results
        if($count>0) {
            $index = 0;
            while(empty($filteredMeetings[$index])){
                $index++;
            }
            $this->assertStringContainsString($filteredMeetings[$index]->getUser()->getUsername(), $client->getResponse()->getContent());
        }
    }

    public function testFilterByLibrary()
    {
        $client = static::createClient();

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $testUser = $userRepository->findBy(['username'=>'opc']);

        // simulate $testUser being logged in
        $client->loginUser($testUser[0]);

        $meetRepo = $entityManager->getRepository(Meeting::class);
        $meetings = $meetRepo->createQueryBuilder('m')
            ->innerJoin('m.book', 'b')
            ->innerJoin('m.location', 'l')
            ->innerJoin('m.user', 'u')
            ->where('b.title LIKE :input')
            ->orWhere('l.name LIKE :input')
            ->orWhere('u.username LIKE :input')
            ->setParameter('input', '%' . 'catholic univers' . '%')
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();

        $attendRepo = $entityManager->getRepository(Attendee::class);
        $attendees = $attendRepo->findBy(
            ['user' => $testUser[0]],
        );

        $attendedMeetingIds = array_map(function($attended) {
            return $attended->getMeeting()->getId();
        }, $attendees);

        $filteredMeetings = array_filter($meetings, function ($meeting) use ($attendedMeetingIds) {
            return !in_array($meeting->getId(), $attendedMeetingIds);
        });
        $count = count($filteredMeetings);

        // mimic the form submission
        $crawler = $client->request('POST', '/filtermeeting', ['meeting_input' => 'catholic univers']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertCount($count, $crawler->filter('.mt-5'));

        // assert that a book with a specific user is in the results
        if($count>0) {
            $index = 0;
            while(empty($filteredMeetings[$index])){
                $index++;
            }
            $this->assertStringContainsString($filteredMeetings[$index]->getUser()->getUsername(), $client->getResponse()->getContent());
        }
    }
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MeetingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Meeting;
use App\Entity\User;
use App\Entity\Attendee;
use Symfony\Bundle\SecurityBundle\Security;

class MeetingController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/meeting', name: 'app_meeting', methods: ['GET', 'POST'])]
    public function index(MeetingRepository $meetRepo, Request $request): Response
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            return $this->joinMeeting($request);
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $userId = null;
        if(!empty($user)){
            $userId = $user->getId();
        }

        $meetings = $meetRepo->createQueryBuilder('m')
            ->where('m.user <> :user')
            ->setParameter('user', $userId)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();

        $attendRepo = $this->entityManager->getRepository(Attendee::class);
        $attendees = $attendRepo->findBy(
            ['user' => $userId]
        );

        $attendedMeetingIds = array_map(function($attended) {
            return $attended->getMeeting()->getId();
        }, $attendees);

        $filteredMeetings = array_filter($meetings, function ($meeting) use ($attendedMeetingIds) {
            return !in_array($meeting->getId(), $attendedMeetingIds);
        });

        return $this->render('meeting/index.html.twig', [
            'controller_name' => 'MeetingController',
            'meetings'=>$filteredMeetings
        ]);
    }

    #[Route('/filtermeeting', name: 'app_filtermeeting', methods: ['GET', 'POST'])]
    public function filter(MeetingRepository $meetRepo, Request $request): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $userId = null;
        if(!empty($user)){
            $userId = $user->getId();
        }

        $input = $request->request->get('meeting_input');

        $meetings = $meetRepo->createQueryBuilder('m')
            ->innerJoin('m.book', 'b')
            ->innerJoin('m.location', 'l')
            ->innerJoin('m.user', 'u')
            ->where('b.title LIKE :input')
            ->orWhere('l.name LIKE :input')
            ->orWhere('u.username LIKE :input')
            ->setParameter('input', '%' . $input . '%')
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();

        $attendRepo = $this->entityManager->getRepository(Attendee::class);
        $attendees = $attendRepo->findBy(
            ['user' => $userId],
        );

        $attendedMeetingIds = array_map(function($attended) {
            return $attended->getMeeting()->getId();
        }, $attendees);

        $filteredMeetings = array_filter($meetings, function ($meeting) use ($attendedMeetingIds) {
            return !in_array($meeting->getId(), $attendedMeetingIds);
        });

        return $this->render('meeting/index.html.twig', [
            'controller_name' => 'MeetingController',
            'meetings'=>$filteredMeetings
        ]);
    }

    public function joinMeeting(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $meetingId = $data['id'];

        $meetingRepo = $this->entityManager->getRepository(Meeting::class);
        $meeting = $meetingRepo->find($meetingId);

        $attendee = new Attendee();
        $attendee->setMeeting($meeting);

        /** @var User $user */
        $user = $this->security->getUser();

        $attendee->setUser($user);

        $this->entityManager->persist($attendee);
        $this->entityManager->flush();

        return new Response('Success', 200);
    }
}

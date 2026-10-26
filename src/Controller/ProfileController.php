<?php

namespace App\Controller;

use App\Entity\Attendee;
use App\Entity\Meeting;
use App\Entity\User;
use App\Entity\FavoriteBook;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(BookRepository $bookRepo, Request $request): Response
    {
        $meetRepo = $this->entityManager->getRepository(Meeting::class);
        $attendRepo = $this->entityManager->getRepository(Attendee::class);


        /** @var User $user */
        $user = $this->security->getUser();
        $username = null;
        $userId = null;
        $userImage = null;
        $bio = null;
        $email = null;
        if(!empty($user)){
            $username = $user->getUsername();
            $userId = $user->getId();
            $userImage = $user->getPicture();
            $bio = $user->getBio();
            $email = $user->getEmail();


        }

        if($request->isMethod('POST')){
            $formData = $request->request->all();
            $filesData = $request->files->all();



            if (isset($formData['BioForm'])) {
                $bioForm = $formData['BioForm'];
                $bio = $bioForm['bio'];
                    $editedUser = $this->entityManager->getRepository(User::class)->find($userId);
                    $editedUser->setBio($bio);
                    $this->entityManager->flush();
            }
            else{
                $this->remove($meetRepo, $request);

            }

        }

        $meetings = $attendRepo->findBy(
            ['user' => $userId]
        );

        usort($meetings, function($a, $b) {
            return $a->getMeeting()->getDate() <=> $b->getMeeting()->getDate();
        });

        $myMeetings = $meetRepo->findBy(
            ['user' => $userId],
            ['date' => 'ASC'],
        );

        $favoriteBooksRepository = $this->entityManager->getRepository(FavoriteBook::class);
        $favoriteBooksLinks = $favoriteBooksRepository->findBy(
            [
                'user' => $user
            ]
        );
        $favoriteBookIds = [];
        foreach ($favoriteBooksLinks as $favoriteBook) {
            $favoriteBookIds[] = $favoriteBook->getBook();
        }
        $favoriteBooks = $bookRepo->findBy(['id' => $favoriteBookIds]);


        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'name' => $username,
            'userimage'=>$userImage,
            'bio' => $bio,
            'newestBooks'=> $favoriteBooks,
            'myMeetings' => $myMeetings,
            'meetings' => $meetings,
            'email'=>$email
        ]);
    }

    //#[Route('/removemeeting', name: 'app_remove', methods: ['POST'])]
    public function remove(MeetingRepository $meetRepo, Request $request): Response{

        $attendId = $request->request->get('attend_id');
        $hostId = $request->request->get('host_id');

        /** @var User $user */
        $user = $this->security->getUser();
        $userId = null;
        if (!empty($user)) {
            $userId = $user->getId();
        }

        $attendRepo = $this->entityManager->getRepository(Attendee::class);

        if(!empty($attendId)) {
            $attendance = $attendRepo->findBy(
                ['meeting' => $attendId,
                    'user' => $userId]
            );

            $this->entityManager->remove($attendance[0]);
            $this->entityManager->flush();

            return new Response('Success', 200);
        }

        if(!empty($hostId)){
            $attendances = $attendRepo->findBy(
                ['meeting' => $hostId]
            );

            foreach ($attendances as $attender) {
                $this->entityManager->remove($attender);
            }

            $host = $meetRepo->find($hostId);
            $this->entityManager->remove($host);
            $this->entityManager->flush();
        }

        return new Response('Success', 200);
    }
}

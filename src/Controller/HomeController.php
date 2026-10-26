<?php

namespace App\Controller;

use App\Entity\Attendee;
use App\Entity\Book;
use App\Entity\FavoriteBook;
use App\Entity\Library;
use App\Entity\Meeting;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    const USERIMAGE = 'userimage';
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $repository = $this->entityManager->getRepository(Book::class);

        $trendingBooks = $repository->findBy(
            ['trending' => true],
            ['id' => 'DESC'],
            10
        );

        $newestBooks = $repository->findBy(
            [],
            ['id'=>'DESC'],
            30
        );

        $allBooks = $repository->findAll();
        $meetRepo = $this->entityManager->getRepository(Meeting::class);
        $attendRepo = $this->entityManager->getRepository(Attendee::class);

        $meetings = $meetRepo->findAll();
        usort($meetings, function($a, $b) {
            return $a->getDate() <=> $b->getDate();
        });

        /** @var User $user */
        $user = $this->security->getUser();
        $username = null;
        $userId = null;
        $userImage = null;
        $bio = null;
        $email= null;
        if(!empty($user)){
            $username = $user->getUsername();
            $userId = $user->getId();
            $userImage = $user->getPicture();
            $bio = $user->getBio();
            $email = $user->getEmail();
        }

        $attendMeetings = $attendRepo->findBy(
            ['user' => $userId]
        );

        usort($attendMeetings, function($a, $b) {
            return $a->getMeeting()->getDate() <=> $b->getMeeting()->getDate();
        });

        $libraryRepository = $this->entityManager->getRepository(Library::class);
        $allLibraries = $libraryRepository->findAll();

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

        $favoriteBooks = $repository->findBy(['id' => $favoriteBookIds]);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'name' => $username,
            'userimage'=>$userImage,
            'bio' => $bio,
            'trendingBooks' => $trendingBooks,
            'newestBooks'=> $newestBooks,
            'meetings'=> $meetings,
            'favoriteBooks' => $favoriteBooks,
            'allBooks'=> $allBooks,
            'allLibraries'=> $allLibraries,
            'attendMeetings' => $attendMeetings,
            'email'=>$email
        ]);
    }

    public function redirectToHomePage(UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $homePageUrl = $urlGenerator->generate('app_home');

        return $this->redirect($homePageUrl);
    }
}

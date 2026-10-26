<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Library;
use App\Entity\Meeting;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

class AddMeetingApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @throws \Exception
     */
    #[Route('/addmeetingapi', name: 'app_addmeetingapi')]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $meeting = new Meeting();

        /** @var User $user */
        $user = $this->security->getUser();
        $userId = null;
        if (!empty($user)) {
            $userId = $user->getId();
        }

        $usersRepository = $this->entityManager->getRepository(User::class);
        $user = $usersRepository->find($userId);

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $book = $bookRepository->find($data["book"]);

        $locationRepository = $this->entityManager->getRepository(Library::class);
        $location = $locationRepository->find($data["library"]);


        $meeting->setUser($user);
        $meeting->setBook($book);
        $meeting->setDescription($data["description"]);
        $meeting->setDate(new DateTime($data["date"]));
        $meeting->setLocation($location);

        $this->entityManager->persist($meeting);
        $this->entityManager->flush();

        return $this->json(['message' => $data]);
    }
}

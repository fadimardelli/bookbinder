<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Entity\Book;
use App\Entity\FavoriteBook;

use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;

class BookController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/book/{id}', name: 'app_book')]
    public function index(Request $request): Response
    {

        $routeParams = $request->attributes->get('_route_params');

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $currentBook = $bookRepository->find($routeParams['id']);

        $reviewsRepository = $this->entityManager->getRepository(Review::class);
        $favoriteBooksRepository = $this->entityManager->getRepository(FavoriteBook::class);
        $availabilityRepository = $this->entityManager->getRepository(Availability::class);
        $availabilities = $availabilityRepository->createQueryBuilder('a')
            ->where('a.book = :bookId')
            ->andWhere('a.current_count != 0')
            ->setParameter('bookId', $routeParams['id'])
            ->getQuery()
            ->getResult();

        /** @var User $user */
        $user = $this->security->getUser();

        $favoriteBooks = $favoriteBooksRepository->findBy(
            [
                'user' => $user
            ]
        );
        $favoriteBookIds = [];
        foreach ($favoriteBooks as $favoriteBook) {
            $favoriteBookIds[] = $favoriteBook->getBook();
        }
        $matchedBooks = $bookRepository->findBy(
            ['id' => $favoriteBookIds]
        );
        $matchedBooksIds = [];
        foreach ($matchedBooks as $book) {
            $matchedBooksIds[] = $book->getId();
        }

        //build the form to submit a review
        $review = new Review();
        $review->setUser($user);
        $review->setBook($currentBook);
        $review->setReview("Write your review here");
        $review->setRating(3);
        $form = $this->createFormBuilder($review)
            ->add('review', TextareaType::class)
            ->add('rating', IntegerType::class, [
                'label' => 'Rating',
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        //handle submitted form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $review = $form->getData();
            $reviewsRepository->save($review, true);
        }

        //get all reviews of the current book
        $reviews = $reviewsRepository->findBy(
            [
                'book' => $currentBook
            ]
        );

        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'bookId' => $routeParams['id'],
            'book' => $currentBook,
            'availabilities' => $availabilities,
            'reviews' => $reviews,
            'favoriteBookIds' => $matchedBooksIds,
            'form' => $form,
        ]);
    }

    public function addToFavorites(Request $request): JsonResponse
    {
        $favoriteBook = $this->getFavoriteBook($request, true);
        $favoriteBooksRepository = $this->entityManager->getRepository(FavoriteBook::class);
        $favoriteBooksRepository->save($favoriteBook, true);
        return new JsonResponse(['success' => true]);
    }

    public function removeFromFavorites(Request $request): JsonResponse
    {
        $favoriteBook = $this->getFavoriteBook($request, false);
        $favoriteBooksRepository = $this->entityManager->getRepository(FavoriteBook::class);
        $favoriteBooksRepository->remove($favoriteBook, true);

        return new JsonResponse(['success' => true]);
    }

    private function getFavoriteBook(Request $request, bool $saving) {
        $content = $request->getContent();
        $data = json_decode($content);
        $bookId = $data->bookId;

        /** @var User $user */
        $user = $this->security->getUser();
        $userId = null;
        if(!empty($user)){
            $userId = $user->getId();
        }

        $bookRepository = $this->entityManager->getRepository(Book::class);
        $usersRepository = $this->entityManager->getRepository(User::class);
        $favoriteBooksRepository = $this->entityManager->getRepository(FavoriteBook::class);
        $book = $bookRepository->findOneBy(
            ['id' => $bookId]
        );
        $user = $usersRepository->findOneBy(
            ['id' => $userId]
        );
        if ($saving) {
            $favoriteBook = new FavoriteBook();
            $favoriteBook->setBook($book);
            $favoriteBook->setUser($user);
        } else {
            $favoriteBook = $favoriteBooksRepository->findOneBy(
                [
                    'book' => $book,
                    'user' => $user
                ]
            );
        }
        return $favoriteBook;
    }
}


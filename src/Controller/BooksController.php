<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Entity\Book;
use App\Entity\Library;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/books/{off}', name: 'app_books',methods: ['GET', 'POST'])]
    public function index(Request $request,BookRepository $bookRepo): Response
    {
        $routeParams = $request->attributes->get('_route_params');

        $repository = $this->entityManager->getRepository(Book::class);

        $libraryRepository = $this->entityManager->getRepository(Library::class);
        $availabilityRepository = $this->entityManager->getRepository(Availability::class);


        $Books = $repository->findBy(
            [],
            ['id' => 'DESC'],
            30,
            (int)$routeParams["off"] *30 - 30
        );

        if ($request->isMethod('POST')) {
            $bookname = $request->request->get('bookname');
            $authorname = $request->request->get('authorname');
            $libraryname = $request->request->get('library');
            $queryBuilder = $bookRepo->createQueryBuilder('b');
            $queryBuilder->orderBy('b.id', 'DESC');


            if (!empty($bookname)) {
                $queryBuilder->andWhere($queryBuilder->expr()->like('b.title', ':bktitle'))
                    ->setParameter('bktitle', '%' . $bookname . '%');
                if (!empty($authorname)) {
                    $queryBuilder->orWhere($queryBuilder->expr()->like('b.author', ':auttitle'))
                        ->setParameter('auttitle', '%' . $authorname . '%');
                }

                if($libraryname !== 'all') {
                    $libraryId = (int) $libraryname;
                    $queryBuilder->join(Availability::class, 'd', 'WITH', 'd.book = b.id')
                        ->andWhere('d.library = :libraryId')
                        ->setParameter('libraryId', $libraryId);

                }


            }

            elseif (!empty($authorname)) {
                $queryBuilder->andWhere($queryBuilder->expr()->like('b.author', ':auttitle'))
                    ->setParameter('auttitle', '%' . $authorname . '%');
                if (!empty($bookname)) {
                    $queryBuilder->orWhere($queryBuilder->expr()->like('b.title', ':bktitle'))
                        ->setParameter('bktitle', '%' . $bookname . '%');

                }
                if($libraryname !== 'all') {
                    $libraryId = (int) $libraryname;
                    $queryBuilder->join(Availability::class, 'd', 'WITH', 'd.book = b.id')
                        ->andWhere('d.library = :libraryId')
                        ->setParameter('libraryId', $libraryId);

                }

            }

            else{
                if($libraryname !== 'all') {
                    $libraryId = (int) $libraryname;
                    $queryBuilder->join('b.availabilities', 'd')
                        ->andWhere('d.library = :library')
                        ->setParameter('library', $libraryId);


                }

            }


            $Books = $queryBuilder->getQuery()->getResult();

        }

// Rest of the code...


        $Libraries = $libraryRepository->findBy([],[]);

        return $this->render('books/index.html.twig', [
            'controller_name' => 'BooksController',
            'books'=>$Books,
            'libraries'=>$Libraries,
            'prev_ind'=>(int)$routeParams["off"] ===1 ? 1 :(int)$routeParams["off"] - 1,
            'next_ind'=>(int)$routeParams["off"] + 1
        ]);
    }
}

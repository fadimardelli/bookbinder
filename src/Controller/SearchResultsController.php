<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchResultsController extends AbstractController
{
    #[Route('/searchresults', name: 'app_searchresults', methods: ['GET', 'POST'])]
    public function index(Request $request, BookRepository $bookRepo): Response
    {
        $books = null;

        if ($request->getMethod() == Request::METHOD_POST) {
            $input = $request->request->get('input');

            $books = $bookRepo->createQueryBuilder('b')
                ->where('b.title LIKE :input')
                ->orWhere('b.author LIKE :input')  // This line is added
                ->setParameter('input', '%' . $input . '%')
                ->getQuery()
                ->getResult();
        }

        return $this->render('searchresults/index.html.twig', [
            'controller_name' => 'SearchresultsController',
            'books' => $books
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\FortuneCookieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager): Response
    {
        // $entityManager->getFilters()
        //     ->enable('fortuneCookie_discontinued')
        //     ->setParameter('discontinued', false);

        $searchTerm = $request->query->get('q');

        if ($searchTerm) {
            $categories = $categoryRepository->search($searchTerm);
        } else {
            $categories = $categoryRepository->findAllOrdered();
        }

        return $this->render('fortune/homepage.html.twig',[
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(int $id, CategoryRepository $categoryRepository, FortuneCookieRepository $fortuneCookieRepository): Response
    {
        $category = $categoryRepository->findWithFortuneCookies($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $stats = $fortuneCookieRepository->countNumberPrintedForCategory($category);

        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category,
            'fortunesPrinted' => $stats->numberPrinted,
            'averagePrinted' => $stats->averagePrinted,
            'categoryName' => $stats->categoryName
        ]);
    }
}

<?php

namespace App\Controller\Category;

use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function index(
        Request $request,
        CategoryRepository $categoryRepository
    ) {
        $categories = $categoryRepository->findAll();

        return $this->render('category/admin/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}

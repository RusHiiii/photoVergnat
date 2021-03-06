<?php

namespace App\Controller\Category;

use App\Entity\WebApp\Category;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Service\WebApp\Category\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/categorie/{slug}_{id}", name="app_category")
     */
    public function index(
        Request $request,
        string $slug,
        Category $category,
        CategoryRepository $categoryRepository
    ) {
        if (!$category->getActive()) {
            return $this->redirectToRoute('app_home');
        }

        $categories = $categoryRepository->findByActive(true);

        return $this->render('category/app/index.html.twig', [
            'category' => $category,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categorie/{slug}_{id}/photo", name="app_category_photo")
     */
    public function gallery(
        Request $request,
        string $slug,
        Category $category,
        CategoryService $categoryService
    ) {
        if (!$category->getActive()) {
            return $this->redirectToRoute('app_home');
        }

        $photos = $categoryService->filterPhotoByType($category->getPhotos());

        return $this->render('category/app/gallery.html.twig', [
            'category' => $category,
            'photos' => $photos
        ]);
    }
}

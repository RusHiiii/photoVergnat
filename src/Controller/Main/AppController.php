<?php

namespace App\Controller\Main;

use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        PhotoRepository $photoRepository
    ) {
        $categories = $categoryRepository->findByActive(true);
        $photos = $photoRepository->findByUsed('Paysage');

        return $this->render('main/app/index.html.twig', [
            'categories' => $categories,
            'photos' => $photos
        ]);
    }
}

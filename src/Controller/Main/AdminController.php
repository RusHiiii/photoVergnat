<?php

namespace App\Controller\Main;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function index(
        Request $request,
        UserRepository $userRepository,
        TagRepository $tagRepository,
        PhotoRepository $photoRepository,
        CategoryRepository $categoryRepository
    ) {
        $users = $userRepository->findAll();
        $tags = $tagRepository->findAll();
        $photos = $photoRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('main/admin/index.html.twig', [
            'users' => $users,
            'tags' => $tags,
            'photos' => $photos,
            'categories' => $categories
        ]);
    }
}

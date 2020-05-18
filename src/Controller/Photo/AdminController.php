<?php

namespace App\Controller\Photo;

use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/photos", name="admin_photos")
     */
    public function index(
        Request $request,
        PhotoRepository $photoRepository
    ) {
        $photos = $photoRepository->findBy([], ['created' => 'ASC']);

        return $this->render('photo/admin/index.html.twig', [
            'photos' => $photos,
        ]);
    }
}

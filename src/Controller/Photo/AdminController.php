<?php

namespace App\Controller\Photo;

use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
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
        $photos = $photoRepository->findAll();

        return $this->render('photo/admin/index.html.twig', [
            'photos' => $photos,
        ]);
    }
}

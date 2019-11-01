<?php

namespace App\Controller\Type;

use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/types", name="admin_types")
     */
    public function index(
        Request $request,
        TypeRepository $typeRepository
    ) {
        $types = $typeRepository->findAll();

        return $this->render('type/admin/index.html.twig', [
            'types' => $types,
        ]);
    }
}

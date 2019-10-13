<?php

namespace App\Controller\Tag;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/tags", name="admin_tags")
     */
    public function index(
        Request $request,
        TagRepository $tagRepository
    ) {
        $tags = $tagRepository->findAll();

        return $this->render('tag/admin/index.html.twig', [
            'tags' => $tags,
        ]);
    }
}

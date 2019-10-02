<?php

namespace App\Controller\Category;

use App\Controller\Security\Voter\CategoryVoter;
use App\Entity\Category;
use App\Repository\SeasonRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Création d'une catégorie
     * @Route("/xhr/admin/category/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request,
        TagRepository $tagRepository,
        SeasonRepository $seasonRepository
    ) {
        $this->denyAccessUnlessGranted(CategoryVoter::VIEW, Category::class);

        $tags = $tagRepository->findByType('category');
        $seasons = $seasonRepository->findAll();

        return $this->render('category/xhr/create.html.twig', [
            'tags' => $tags,
            'seasons' => $seasons
        ]);
    }
}

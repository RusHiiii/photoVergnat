<?php

namespace App\Controller\Season;

use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/seasons", name="admin_seasons")
     */
    public function index(
        Request $request,
        SeasonRepository $seasonRepository
    ) {
        $seasons = $seasonRepository->findAll();

        return $this->render('season/admin/index.html.twig', [
            'seasons' => $seasons,
        ]);
    }
}

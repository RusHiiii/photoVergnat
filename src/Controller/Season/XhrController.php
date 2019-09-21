<?php

namespace App\Controller\Season;

use App\Controller\Security\Voter\SeasonVoter;
use App\Entity\Season;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\Season\SeasonService;
use App\Service\Tag\TagService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Edtion d'une saison
     * @Route("/xhr/admin/season/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        Season $season
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::VIEW, Season::class);

        return $this->render('season/xhr/edit.html.twig', [
            'season' => $season,
        ]);
    }

    /**
     * Création d'une saison
     * @Route("/xhr/admin/season/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::VIEW, Season::class);

        return $this->render('season/xhr/create.html.twig', []);
    }

    /**
     * Suppression d'une saison
     * @Route("/xhr/admin/season/remove", condition="request.isXmlHttpRequest()")
     */
    public function removeSeason(
        Request $request,
        SeasonService $seasonService
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::REMOVE, Season::class);

        $data = $request->request->all();
        $resultRemove = $seasonService->removeSeason($data['season']);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }

    /**
     * Création d'une saison
     * @Route("/xhr/admin/season/create", condition="request.isXmlHttpRequest()")
     */
    public function createSeason(
        Request $request,
        SeasonService $seasonService
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::CREATE, Season::class);

        $data = $request->request->all();
        $resultCreate = $seasonService->createSeason($data['season']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'season' => $resultCreate['season']
        ]);
    }

    /**
     * MàJ d'une saison
     * @Route("/xhr/admin/season/update", condition="request.isXmlHttpRequest()")
     */
    public function updateSeason(
        Request $request,
        SeasonService $seasonService
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::EDIT, Season::class);

        $data = $request->request->all();
        $resultUpdate = $seasonService->updateSeason($data['season']);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'season' => $resultUpdate['season']
        ]);
    }
}

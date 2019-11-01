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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
        $this->denyAccessUnlessGranted(SeasonVoter::VIEW, $season);

        return $this->render('season/xhr/edit.html.twig', [
            'season' => $season,
        ]);
    }

    /**
     * Suppression d'une saison
     * @Route("/xhr/admin/season/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeSeason(
        Request $request,
        SeasonService $seasonService,
        Season $season
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::REMOVE, $season);

        $resultRemove = $seasonService->removeSeason($season);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }

    /**
     * MàJ d'une saison
     * @Route("/xhr/admin/season/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updateSeason(
        Request $request,
        SeasonService $seasonService,
        Season $season
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::EDIT, $season);

        $data = $request->request->all();
        $resultUpdate = $seasonService->updateSeason($data['season'], $season);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'season' => $resultUpdate['season']
        ]);
    }

    /**
     * Création d'une saison
     * @Route("/xhr/admin/season/create", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function createSeason(
        Request $request,
        SeasonService $seasonService
    ) {
        $data = $request->request->all();
        $resultCreate = $seasonService->createSeason($data['season']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'season' => $resultCreate['season']
        ]);
    }

    /**
     * Création d'une saison
     * @Route("/xhr/admin/season/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function displayModalCreate(
        Request $request
    ) {
        return $this->render('season/xhr/create.html.twig', []);
    }
}

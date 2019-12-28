<?php

namespace App\Controller\Season;

use App\Controller\Security\Voter\SeasonVoter;
use App\Entity\WebApp\Season;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\User;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Season\Exceptions\SeasonInvalidDataException;
use App\Service\WebApp\Season\Exceptions\SeasonNotFoundException;
use App\Service\WebApp\Season\SeasonService;
use App\Service\WebApp\Tag\TagService;
use App\Service\WebApp\User\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class XhrController extends AbstractController
{
    private $serializer;
    private $errorFactory;

    public function __construct(
        SerializerInterface $serializer,
        ErrorFactory $errorFactory
    ) {
        $this->serializer = $serializer;
        $this->errorFactory = $errorFactory;
    }

    /**
     * Suppression d'une saison
     * @Route("/xhr/admin/season/remove/{id}", condition="request.isXmlHttpRequest()", methods={"DELETE"})
     */
    public function removeSeason(
        Request $request,
        SeasonService $seasonService,
        Season $season
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::REMOVE, $season);

        $seasonService->removeSeason($season);

        return new JsonResponse([], 200);
    }

    /**
     * MàJ d'une saison
     * @Route("/xhr/admin/season/update/{id}", condition="request.isXmlHttpRequest()", methods={"PATCH"})
     */
    public function updateSeason(
        Request $request,
        SeasonService $seasonService,
        Season $season
    ) {
        $this->denyAccessUnlessGranted(SeasonVoter::EDIT, $season);

        $data = $request->request->all();

        try {
            $resultUpdate = $seasonService->updateSeason($data['season'], $season);
        } catch (SeasonInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (SeasonNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultUpdate, 'json', ['groups' => ['default', 'season']]),
            200
        );
    }

    /**
     * Création d'une saison
     * @Route("/xhr/admin/season/create", condition="request.isXmlHttpRequest()", methods={"POST"})
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function createSeason(
        Request $request,
        SeasonService $seasonService
    ) {
        $data = $request->request->all();

        try {
            $resultCreate = $seasonService->createSeason($data['season']);
        } catch (SeasonInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default', 'season']]),
            200
        );
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
}

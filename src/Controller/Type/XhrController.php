<?php

namespace App\Controller\Type;

use App\Controller\Security\Voter\TagVoter;
use App\Controller\Security\Voter\TypeVoter;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\Type;
use App\Entity\WebApp\User;
use App\Service\WebApp\Tag\TagService;
use App\Service\WebApp\Type\TypeService;
use App\Service\WebApp\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class XhrController extends AbstractController
{
    /**
     * Suppression d'un type
     * @Route("/xhr/admin/type/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeType(
        Request $request,
        TypeService $typeService,
        Type $type
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::REMOVE, $type);

        $resultRemove = $typeService->removeType($type);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }

    /**
     * MàJ d'un type
     * @Route("/xhr/admin/type/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updateType(
        Request $request,
        TypeService $typeService,
        Type $type
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::EDIT, $type);

        $data = $request->request->all();
        $resultUpdate = $typeService->updateType($data['type'], $type);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'type' => $resultUpdate['type']
        ]);
    }

    /**
     * Edtion d'un type
     * @Route("/xhr/admin/type/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        Type $type
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::VIEW, $type);

        return $this->render('type/xhr/edit.html.twig', [
            'type' => $type,
        ]);
    }

    /**
     * Création d'un type
     * @Route("/xhr/admin/type/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function displayModalCreate(
        Request $request
    ) {
        return $this->render('type/xhr/create.html.twig', []);
    }

    /**
     * Création d'un type
     * @Route("/xhr/admin/type/create", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createType(
        Request $request,
        TypeService $typeService
    ) {
        $data = $request->request->all();
        $resultCreate = $typeService->createType($data['type']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'type' => $resultCreate['type']
        ]);
    }
}

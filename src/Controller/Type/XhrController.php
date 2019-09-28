<?php

namespace App\Controller\Type;

use App\Controller\Security\Voter\TagVoter;
use App\Controller\Security\Voter\TypeVoter;
use App\Entity\Tag;
use App\Entity\Type;
use App\Entity\User;
use App\Service\Tag\TagService;
use App\Service\Type\TypeService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Création d'un type
     * @Route("/xhr/admin/type/create", condition="request.isXmlHttpRequest()")
     */
    public function createType(
        Request $request,
        TypeService $typeService
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::CREATE, Type::class);

        $data = $request->request->all();
        $resultCreate = $typeService->createType($data['type']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'type' => $resultCreate['type']
        ]);
    }

    /**
     * Suppression d'un type
     * @Route("/xhr/admin/type/remove", condition="request.isXmlHttpRequest()")
     */
    public function removeType(
        Request $request,
        TypeService $typeService
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::REMOVE, Type::class);

        $data = $request->request->all();
        $resultRemove = $typeService->removeType($data['type']);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }

    /**
     * MàJ d'un type
     * @Route("/xhr/admin/type/update", condition="request.isXmlHttpRequest()")
     */
    public function updateType(
        Request $request,
        TypeService $typeService
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::EDIT, Type::class);

        $data = $request->request->all();
        $resultUpdate = $typeService->updateType($data['type']);

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
        $this->denyAccessUnlessGranted(TypeVoter::VIEW, Type::class);

        return $this->render('type/xhr/edit.html.twig', [
            'type' => $type,
        ]);
    }

    /**
     * Création d'un type
     * @Route("/xhr/admin/type/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::VIEW, Type::class);

        return $this->render('type/xhr/create.html.twig', []);
    }
}

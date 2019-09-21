<?php

namespace App\Controller\Tag;

use App\Controller\Security\Voter\TagVoter;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\Tag\TagService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Création d'un tag
     * @Route("/xhr/admin/tag/create", condition="request.isXmlHttpRequest()")
     */
    public function createTag(
        Request $request,
        TagService $tagService
    ) {
        $this->denyAccessUnlessGranted(TagVoter::CREATE, Tag::class);

        $data = $request->request->all();
        $resultCreate = $tagService->createTag($data['tag']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'tag' => $resultCreate['tag']
        ]);
    }

    /**
     * MàJ d'un tag
     * @Route("/xhr/admin/tag/update", condition="request.isXmlHttpRequest()")
     */
    public function updateTag(
        Request $request,
        TagService $tagService
    ) {
        $this->denyAccessUnlessGranted(TagVoter::EDIT, Tag::class);

        $data = $request->request->all();
        $resultUpdate = $tagService->updateTag($data['tag']);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'tag' => $resultUpdate['tag']
        ]);
    }

    /**
     * Suppression d'un tag
     * @Route("/xhr/admin/tag/remove", condition="request.isXmlHttpRequest()")
     */
    public function removeTag(
        Request $request,
        TagService $tagService
    ) {
        $this->denyAccessUnlessGranted(TagVoter::REMOVE, Tag::class);

        $data = $request->request->all();
        $resultRemove = $tagService->removeTag($data['tag']);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }

    /**
     * Edtion d'un tag
     * @Route("/xhr/admin/tag/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        Tag $tag
    ) {
        $this->denyAccessUnlessGranted(TagVoter::VIEW, Tag::class);

        return $this->render('tag/xhr/edit.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/admin/tag/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request
    ) {
        $this->denyAccessUnlessGranted(TagVoter::VIEW, Tag::class);

        return $this->render('tag/xhr/create.html.twig', []);
    }
}

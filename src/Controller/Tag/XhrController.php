<?php

namespace App\Controller\Tag;

use App\Controller\Security\Voter\TagVoter;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\User;
use App\Service\WebApp\Tag\TagService;
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
     * MàJ d'un tag
     * @Route("/xhr/admin/tag/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updateTag(
        Request $request,
        TagService $tagService,
        Tag $tag
    ) {
        $this->denyAccessUnlessGranted(TagVoter::EDIT, $tag);

        $data = $request->request->all();
        $resultUpdate = $tagService->updateTag($data['tag'], $tag);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'tag' => $resultUpdate['tag']
        ]);
    }

    /**
     * Suppression d'un tag
     * @Route("/xhr/admin/tag/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeTag(
        Request $request,
        TagService $tagService,
        Tag $tag
    ) {
        $this->denyAccessUnlessGranted(TagVoter::REMOVE, $tag);

        $resultRemove = $tagService->removeTag($tag);

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
        $this->denyAccessUnlessGranted(TagVoter::VIEW, $tag);

        return $this->render('tag/xhr/edit.html.twig', [
            'tag' => $tag,
        ]);
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/admin/tag/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function displayModalCreate(
        Request $request
    ) {
        return $this->render('tag/xhr/create.html.twig', []);
    }

    /**
     * Création d'un tag
     * @Route("/xhr/admin/tag/create", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function createTag(
        Request $request,
        TagService $tagService
    ) {
        $data = $request->request->all();
        $resultCreate = $tagService->createTag($data['tag']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'tag' => $resultCreate['tag']
        ]);
    }
}

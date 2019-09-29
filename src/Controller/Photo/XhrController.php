<?php

namespace App\Controller\Photo;

use App\Controller\Security\Voter\PhotoVoter;
use App\Controller\Security\Voter\TagVoter;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\TypeRepository;
use App\Service\Photo\PhotoService;
use App\Service\Tag\TagService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Création d'une photo
     * @Route("/xhr/admin/photo/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request,
        TagRepository $tagRepository,
        TypeRepository $typeRepository
    )
    {
        $this->denyAccessUnlessGranted(PhotoVoter::VIEW, Photo::class);

        $tags = $tagRepository->findByType('photo');
        $formats = $typeRepository->findAll();

        return $this->render('photo/xhr/create.html.twig', [
            'tags' => $tags,
            'formats' => $formats
        ]);
    }

    /**
     * Création d'une photo
     * @Route("/xhr/admin/photo/create", condition="request.isXmlHttpRequest()")
     */
    public function createPhoto(
        Request $request,
        PhotoService $photoService
    )
    {
        $this->denyAccessUnlessGranted(PhotoVoter::CREATE, Photo::class);

        $data = $request->request->all();
        $file = $request->files->get('file');
        $resultCreate = $photoService->createPhoto($data, $file);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'photo' => $resultCreate['photo']
        ]);
    }

    /**
     * Suppression d'une photo
     * @Route("/xhr/admin/photo/remove", condition="request.isXmlHttpRequest()")
     */
    public function removePhoto(
        Request $request,
        PhotoService $photoService
    )
    {
        $this->denyAccessUnlessGranted(PhotoVoter::REMOVE, Photo::class);

        $data = $request->request->all();
        $resultRemove = $photoService->removePhoto($data['photo']);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);

    }
}

<?php

namespace App\Controller\Photo;

use App\Controller\Security\Voter\TagVoter;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\TypeRepository;
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
    ) {
        //$this->denyAccessUnlessGranted(SeasonVoter::VIEW, Season::class);

        $tags = $tagRepository->findByType('photo');
        $formats = $typeRepository->findAll();

        return $this->render('photo/xhr/create.html.twig', [
            'tags' => $tags,
            'formats' => $formats
        ]);
    }
}

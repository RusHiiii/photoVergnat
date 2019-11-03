<?php

namespace App\Controller\Photo;

use App\Controller\Security\Voter\PhotoVoter;
use App\Controller\Security\Voter\TagVoter;
use App\Entity\WebApp\Photo;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\User;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Photo\Exceptions\PhotoInvalidDataException;
use App\Service\WebApp\Photo\Exceptions\PhotoNotFoundException;
use App\Service\WebApp\Photo\PhotoService;
use App\Service\WebApp\Tag\Exceptions\TagNotFoundException;
use App\Service\WebApp\Tag\TagService;
use App\Service\WebApp\Type\Exceptions\TypeNotFoundException;
use App\Service\WebApp\User\UserService;
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
     * Suppression d'une photo
     * @Route("/xhr/admin/photo/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removePhoto(
        Request $request,
        PhotoService $photoService,
        Photo $photo
    ) {
        $this->denyAccessUnlessGranted(PhotoVoter::REMOVE, $photo);

        $photoService->removePhoto($photo);

        return new JsonResponse([], 200);
    }

    /**
     * Edition d'une photo
     * @Route("/xhr/admin/photo/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updatePhoto(
        Request $request,
        PhotoService $photoService,
        Photo $photo
    ) {
        $this->denyAccessUnlessGranted(PhotoVoter::EDIT, $photo);

        $data = $request->request->all();
        $file = $request->files->get('file');

        try {
            $resultUpdate = $photoService->updatePhoto($data, $file, $photo);
        } catch (PhotoInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (TagNotFoundException | TypeNotFoundException | PhotoNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultUpdate, 'json', ['groups' => ['default', 'photo']]),
            200
        );
    }

    /**
     * Création d'une photo
     * @Route("/xhr/admin/photo/create", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function createPhoto(
        Request $request,
        PhotoService $photoService
    ) {
        $data = $request->request->all();
        $file = $request->files->get('file');

        try {
            $resultCreate = $photoService->createPhoto($data, $file);
        } catch (PhotoInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (TagNotFoundException | TypeNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default', 'photo']]),
            200
        );
    }

    /**
     * Création d'une photo
     * @Route("/xhr/admin/photo/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_AUTHOR')")
     */
    public function displayModalCreate(
        Request $request,
        TagRepository $tagRepository,
        TypeRepository $typeRepository
    ) {
        $tags = $tagRepository->findByType('photo');
        $formats = $typeRepository->findAll();

        return $this->render('photo/xhr/create.html.twig', [
            'tags' => $tags,
            'formats' => $formats
        ]);
    }

    /**
     * Edition d'une photo
     * @Route("/xhr/admin/photo/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        TagRepository $tagRepository,
        TypeRepository $typeRepository,
        Photo $photo
    ) {
        $this->denyAccessUnlessGranted(PhotoVoter::EDIT, $photo);

        $tags = $tagRepository->findByType('photo');
        $formats = $typeRepository->findAll();

        return $this->render('photo/xhr/edit.html.twig', [
            'tags' => $tags,
            'formats' => $formats,
            'photo' => $photo
        ]);
    }
}

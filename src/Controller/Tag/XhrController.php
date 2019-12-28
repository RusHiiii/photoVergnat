<?php

namespace App\Controller\Tag;

use App\Controller\Security\Voter\TagVoter;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\User;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Tag\Exceptions\TagInvalidDataException;
use App\Service\WebApp\Tag\Exceptions\TagNotFoundException;
use App\Service\WebApp\Tag\TagService;
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

        try {
            $resultUpdate = $tagService->updateTag($data['tag'], $tag);
        } catch (TagInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (TagNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultUpdate, 'json', ['groups' => ['default', 'tag']]),
            200
        );
    }

    /**
     * Suppression d'un tag
     * @Route("/xhr/admin/tag/remove/{id}", condition="request.isXmlHttpRequest()", methods={"DELETE"})
     */
    public function removeTag(
        Request $request,
        TagService $tagService,
        Tag $tag
    ) {
        $this->denyAccessUnlessGranted(TagVoter::REMOVE, $tag);

        $tagService->removeTag($tag);

        return new JsonResponse([], 200);
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

        try {
            $resultCreate = $tagService->createTag($data['tag']);
        } catch (TagInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default', 'tag']]),
            200
        );
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
}

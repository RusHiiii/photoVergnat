<?php

namespace App\Controller\Type;

use App\Controller\Security\Voter\TypeVoter;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\Type;
use App\Entity\WebApp\User;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Tag\TagService;
use App\Service\WebApp\Type\Exceptions\InvalidDataException;
use App\Service\WebApp\Type\Exceptions\NotFoundException;
use App\Service\WebApp\Type\TypeService;
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
     * Suppression d'un type
     * @Route("/xhr/admin/type/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeType(
        Request $request,
        TypeService $typeService,
        Type $type
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::REMOVE, $type);

        $typeService->removeType($type);

        return new JsonResponse([], 200);
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

        try {
            $data = $request->request->all();
            $resultUpdate = $typeService->updateType($data['type'], $type);
        } catch (NotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        } catch (InvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultUpdate, 'json'),
            200
        );
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
        try {
            $data = $request->request->all();
            $resultCreate = $typeService->createType($data['type']);
        } catch (InvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json'),
            200
        );
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
}

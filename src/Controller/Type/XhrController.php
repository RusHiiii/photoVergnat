<?php

namespace App\Controller\Type;

use App\Controller\Security\Voter\TypeVoter;
use App\Entity\Core\SerializedResponse;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\Type;
use App\Entity\WebApp\User;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\Tag\TagService;
use App\Service\WebApp\Type\Exceptions\TypeInvalidDataException;
use App\Service\WebApp\Type\Exceptions\TypeNotFoundException;
use App\Service\WebApp\Type\Exceptions\UserNotFoundException;
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
     * @Route("/xhr/admin/type/remove/{id}", condition="request.isXmlHttpRequest()", methods={"DELETE"})
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
     * @Route("/xhr/admin/type/update/{id}", condition="request.isXmlHttpRequest()", methods={"PATCH"})
     */
    public function updateType(
        Request $request,
        TypeService $typeService,
        Type $type
    ) {
        $this->denyAccessUnlessGranted(TypeVoter::EDIT, $type);

        $data = $request->request->all();

        try {
            $resultUpdate = $typeService->updateType($data['type'], $type);
        } catch (TypeNotFoundException $e) {
            return new SerializedResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        } catch (TypeInvalidDataException $e) {
            return new SerializedResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new SerializedResponse(
            $this->serializer->serialize($resultUpdate, 'json'),
            200
        );
    }

    /**
     * Création d'un type
     * @Route("/xhr/admin/type/create", condition="request.isXmlHttpRequest()", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createType(
        Request $request,
        TypeService $typeService
    ) {
        $data = $request->request->all();

        try {
            $resultCreate = $typeService->createType($data['type']);
        } catch (TypeInvalidDataException $e) {
            return new SerializedResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new SerializedResponse(
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

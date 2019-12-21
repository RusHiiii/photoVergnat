<?php

namespace App\Controller\User;

use App\Controller\Security\Voter\UserVoter;
use App\Entity\WebApp\User;
use App\Service\Tools\Error\Factory\ErrorFactory;
use App\Service\WebApp\User\Exceptions\UserInvalidDataException;
use App\Service\WebApp\User\Exceptions\UserNotFoundException;
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
     * Edtion d'un utilisateur
     * @Route("/xhr/app/user/edit-user/", condition="request.isXmlHttpRequest()")
     */
    public function updateProfile(
        Request $request,
        UserService $userService
    ) {
        $data = $request->request->all();

        try {
            $resultEdit = $userService->updateProfile($data['user'], $this->getUser());
        } catch (UserInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (UserNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultEdit, 'json', ['groups' => ['default']]),
            200
        );
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/app/user/edit-password/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updatePassword(
        Request $request,
        UserService $userService,
        User $user
    ) {
        $this->denyAccessUnlessGranted(UserVoter::EDIT_PSWD, $user);

        $data = $request->request->all();

        try {
            $resultEdit = $userService->updatePassword($data['user'], $user);
        } catch (UserInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (UserNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultEdit, 'json', ['groups' => ['default']]),
            200
        );
    }

    /**
     * Suppression d'un utilisateur
     * @Route("/xhr/admin/user/remove/{id}", condition="request.isXmlHttpRequest()")
     */
    public function removeUser(
        Request $request,
        UserService $userService,
        User $user
    ) {
        $this->denyAccessUnlessGranted(UserVoter::REMOVE, $user);

        $userService->removeUser($user);

        return new JsonResponse([], 200);
    }

    /**
     * MàJ d'un utilisateur
     * @Route("/xhr/admin/user/update/{id}", condition="request.isXmlHttpRequest()")
     */
    public function updateUser(
        Request $request,
        UserService $userService,
        User $user
    ) {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $data = $request->request->all();

        try {
            $resultUpdate = $userService->updateUser($data['user'], $user);
        } catch (UserInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        } catch (UserNotFoundException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                404
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultUpdate, 'json', ['groups' => ['default']]),
            200
        );
    }

    /**
     * Création d'un utilisateur
     * @Route("/xhr/admin/user/create", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function createUser(
        Request $request,
        UserService $userService
    ) {
        $data = $request->request->all();

        try {
            $resultCreate = $userService->createUser($data['user']);
        } catch (UserInvalidDataException $e) {
            return new JsonResponse(
                $this->serializer->serialize($this->errorFactory->create($e), 'json'),
                400
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($resultCreate, 'json', ['groups' => ['default']]),
            200
        );
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/admin/user/display/edit/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalEdit(
        Request $request,
        User $user
    ) {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);

        return $this->render('user/xhr/edit.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/admin/user/display/password/{id}", condition="request.isXmlHttpRequest()")
     */
    public function displayModalPassword(
        Request $request,
        User $user
    ) {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);

        return $this->render('user/xhr/password.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/admin/user/display/create/", condition="request.isXmlHttpRequest()")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function displayModalCreate(
        Request $request
    ) {
        return $this->render('user/xhr/create.html.twig', []);
    }
}

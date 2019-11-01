<?php

namespace App\Controller\User;

use App\Controller\Security\Voter\UserVoter;
use App\Entity\User;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class XhrController extends AbstractController
{
    /**
     * Edtion d'un utilisateur
     * @Route("/xhr/app/user/edit-user/", condition="request.isXmlHttpRequest()")
     */
    public function updateProfile(
        Request $request,
        UserService $userService
    ) {
        $data = $request->request->all();
        $resultEdit = $userService->updateProfile($data['user']);

        return new JsonResponse([
            'errors' => $resultEdit['errors']
        ]);
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
        $resultEdit = $userService->updatePassword($data['user'], $user);

        return new JsonResponse([
            'errors' => $resultEdit['errors']
        ]);
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

        $resultRemove = $userService->removeUser($user);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
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
        $resultUpdate = $userService->updateUser($data['user'], $user);

        return new JsonResponse([
            'errors' => $resultUpdate['errors'],
            'user' => $resultUpdate['user']
        ]);
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
        $resultCreate = $userService->createUser($data['user']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'user' => $resultCreate['user']
        ]);
    }
}

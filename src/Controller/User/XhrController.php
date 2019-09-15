<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Edtion d'un utilisateur
     * @Route("/xhr/app/user/edit-user", condition="request.isXmlHttpRequest()")
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
     * @Route("/xhr/app/user/edit-password", condition="request.isXmlHttpRequest()")
     */
    public function updatePassword(
        Request $request,
        UserService $userService
    ) {
        $data = $request->request->all();
        $resultEdit = $userService->updatePassword($data['user']);

        return new JsonResponse([
            'errors' => $resultEdit['errors']
        ]);
    }

    /**
     * Suppression d'un utilisateur
     * @Route("/xhr/admin/user/remove", condition="request.isXmlHttpRequest()")
     */
    public function removeUser(
        Request $request,
        UserService $userService
    ) {
        $this->denyAccessUnlessGranted('remove', User::class);

        $data = $request->request->all();
        $resultRemove = $userService->removeUser($data['user']);

        return new JsonResponse([
            'errors' => $resultRemove['errors']
        ]);
    }

    /**
     * Création d'un utilisateur
     * @Route("/xhr/admin/user/create", condition="request.isXmlHttpRequest()")
     */
    public function createUser(
        Request $request,
        UserService $userService
    ) {
        $this->denyAccessUnlessGranted('create', User::class);

        $data = $request->request->all();
        $resultCreate = $userService->createUser($data['user']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'user' => $resultCreate['user']
        ]);
    }

    /**
     * MàJ d'un utilisateur
     * @Route("/xhr/admin/user/update", condition="request.isXmlHttpRequest()")
     */
    public function updateUser(
        Request $request,
        UserService $userService
    ) {
        $this->denyAccessUnlessGranted('edit', User::class);

        $data = $request->request->all();
        $resultUpdate = $userService->updateUser($data['user']);

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
        $this->denyAccessUnlessGranted('view', User::class);

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
        $this->denyAccessUnlessGranted('view', User::class);

        return $this->render('user/xhr/password.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edtion d'un mot de passe
     * @Route("/xhr/admin/user/display/create/", condition="request.isXmlHttpRequest()")
     */
    public function displayModalCreate(
        Request $request
    ) {
        $this->denyAccessUnlessGranted('view', User::class);

        return $this->render('user/xhr/create.html.twig', []);
    }
}

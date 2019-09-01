<?php

namespace App\Controller\Xhr;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * Edtion d'un utilisateur
     * @Route("/xhr/app/edit-user", condition="request.isXmlHttpRequest()")
     */
    public function editUser(
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
     * @Route("/xhr/app/edit-password", condition="request.isXmlHttpRequest()")
     */
    public function editPassword(
        Request $request,
        UserService $userService
    ) {
        $data = $request->request->all();

        $resultEdit = $userService->updatePassword($data['user']);

        return new JsonResponse([
            'errors' => $resultEdit['errors']
        ]);
    }
}

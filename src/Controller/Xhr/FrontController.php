<?php

namespace App\Controller\Xhr;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * Edtion d'un utilisateur
     *
     * @Route("/xhr/front/edit-user", condition="request.isXmlHttpRequest()")
     */
    public function editUser(
        Request $request,
        UserService $userService
    ) {
        $data = $request->request->all();

        $resultEdit = $userService->updateUser($data['user']);

        return new JsonResponse([
            'errors' => $resultEdit['errors']
        ]);
    }
}

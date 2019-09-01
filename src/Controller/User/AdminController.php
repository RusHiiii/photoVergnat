<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index(
        Request $request,
        UserRepository $userRepository
    )
    {
        $users = $userRepository->findAll();

        return $this->render('user/admin/index.html.twig', [
            'users' => $users,
        ]);
    }
}

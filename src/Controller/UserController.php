<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="app_profil")
     */
    public function index(
        Request $request
    )
    {
        $currentUser = $this->getUser();

        return $this->render('user/index.html.twig', [
            'user' => $currentUser,
        ]);
    }
}

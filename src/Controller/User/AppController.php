<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="app_profil")
     */
    public function index(
        Request $request
    )
    {
        $currentUser = $this->getUser();

        return $this->render('user/app/index.html.twig', [
            'user' => $currentUser,
        ]);
    }
}

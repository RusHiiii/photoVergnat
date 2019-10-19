<?php

namespace App\Controller\Information;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\Category\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(
        Request $request
    ) {
        return $this->render('information/app/contact.html.twig', []);
    }

    /**
     * @Route("/a-propos", name="app_information")
     */
    public function information(
        Request $request
    ) {
        return $this->render('information/app/information.html.twig', []);
    }
}

<?php

namespace App\Controller\Information;

use App\Service\Comment\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Envoie d'un message
     * @Route("/xhr/app/information/contact/send", condition="request.isXmlHttpRequest()")
     */
    public function sendContact(
        Request $request
    ) {

    }
}

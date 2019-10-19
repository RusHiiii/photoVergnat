<?php

namespace App\Controller\Information;

use App\Service\Comment\CommentService;
use App\Service\Information\InformationService;
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
        Request $request,
        InformationService $informationService
    ) {
        $data = $request->request->all();
        $resultSend = $informationService->sendContactMail($data['mail']);

        return new JsonResponse([
            'errors' => $resultSend['errors']
        ]);
    }
}

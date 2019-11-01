<?php

namespace App\Controller\Comment;

use App\Service\WebApp\Comment\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class XhrController extends AbstractController
{
    /**
     * Création d'un commentaire
     * @Route("/xhr/app/comment/create", condition="request.isXmlHttpRequest()")
     */
    public function createComment(
        Request $request,
        CommentService $commentService
    ) {
        $data = $request->request->all();
        $resultCreate = $commentService->createComment($data['comment']);

        return new JsonResponse([
            'errors' => $resultCreate['errors'],
            'comment' => $resultCreate['comment']
        ]);
    }
}

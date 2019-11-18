<?php

namespace App\Controller\Comment;

use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_comments")
     */
    public function index(
        Request $request,
        CommentRepository $commentRepository
    ) {
        $comments = $commentRepository->findAll();

        return $this->render('comment/admin/index.html.twig', [
            'comments' => $comments,
        ]);
    }
}

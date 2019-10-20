<?php

namespace App\Controller\Main;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PhotoRepository;
use App\Repository\Statistic\StatisticRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Service\Statistic\StatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function index(
        Request $request,
        CommentRepository $commentRepository,
        StatisticService $statisticService
    ) {
        $items = $statisticService->getItems();
        $comments = $commentRepository->findByLast(4);
        $lastActions = $statisticService->getLastUpdate();

        return $this->render('main/admin/index.html.twig', [
            'items' => $items,
            'comments' => $comments,
            'lastActions' => $lastActions
        ]);
    }
}

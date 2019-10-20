<?php

namespace App\Controller\Information;

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
     * @Route("/admin/statistics", name="admin_statistics")
     */
    public function statistics(
        Request $request,
        StatisticService $statisticService
    ) {
        $items = $statisticService->getItems();

        return $this->render('information/admin/statistics.html.twig', [
            'items' => $items
        ]);
    }
}

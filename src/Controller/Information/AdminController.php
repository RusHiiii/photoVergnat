<?php

namespace App\Controller\Information;

use App\Entity\WebApp\User;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\Statistic\StatisticRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\WebApp\Category\CategoryService;
use App\Service\WebApp\Statistic\StatisticService;
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
        StatisticService $statisticService,
        CategoryService $categoryService
    ) {
        $items = $statisticService->getItems();
        $popular = $categoryService->getCategoriesPopularity();

        return $this->render('information/admin/statistics.html.twig', [
            'items' => $items,
            'articles' => $popular
        ]);
    }
}

<?php

namespace App\Controller\Main;

use App\Entity\WebApp\User;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Comment\Doctrine\CommentRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\Statistic\StatisticRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\WebApp\Statistic\StatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("/admin/phpinfo")
     */
    public function info(
        Request $request
    ) {
        ob_start();
        phpinfo();
        $str = ob_get_contents();
        ob_get_clean();

        return new Response($str);
    }
}

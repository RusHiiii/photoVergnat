<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Statistic;

use App\Entity\WebApp\Season;
use App\Entity\WebApp\Tag;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\WebApp\Category\CategoryService;
use App\Service\WebApp\Photo\PhotoService;
use App\Service\WebApp\Tag\TagService;
use App\Service\Tools\ToolsService;
use App\Service\WebApp\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class StatisticService
{
    private $userService;
    private $categoryService;
    private $toolsService;
    private $photoService;
    private $tagService;
    private $userRepository;
    private $tagRepository;
    private $photoRepository;
    private $categoryRepository;

    public function __construct(
        UserService $userService,
        CategoryService $categoryService,
        TagService $tagService,
        PhotoService $photoService,
        ToolsService $toolsService,
        UserRepository $userRepository,
        TagRepository $tagRepository,
        PhotoRepository $photoRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->userService = $userService;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->photoService = $photoService;
        $this->toolsService = $toolsService;
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
        $this->photoRepository = $photoRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Récupération des dernieres actions
     * @return array
     */
    public function getLastUpdate(): array
    {
        $userStat = $this->userService->getLastAction();
        $categoryStat = $this->categoryService->getLastAction();
        $tagStat = $this->tagService->getLastAction();
        $photoStat = $this->photoService->getLastAction();

        $datas = array_merge($userStat, $categoryStat, $tagStat, $photoStat);

        usort($datas, [$this->toolsService, 'compareByUpdated']);

        return $datas;
    }

    /**
     * Récupération des items
     * @return array
     */
    public function getItems(): array
    {
        $data = [];

        $data['users'] = [
            'items' => $this->userRepository->findAll(),
            'icon' => 'users'
        ];

        $data['tags'] = [
            'items' => $this->tagRepository->findAll(),
            'icon' => 'tag'
        ];

        $data['photos'] = [
            'items' => $this->photoRepository->findAll(),
            'icon' => 'camera'
        ];

        $data['categories'] = [
            'items' => $this->categoryRepository->findAll(),
            'icon' => 'file-text'
        ];

        return $data;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 05/10/2019
 * Time: 15:58
 */

namespace App\Menu;

use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Service\Tools\ToolsService;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class MenuBuilder
{
    private $factory;
    private $security;
    private $seasonRepository;
    private $toolsService;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(
        FactoryInterface $factory,
        Security $security,
        SeasonRepository $seasonRepository,
        ToolsService $toolsService
    ) {
        $this->factory = $factory;
        $this->security = $security;
        $this->seasonRepository = $seasonRepository;
        $this->toolsService = $toolsService;
    }

    public function main(RequestStack $requestStack)
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => ['id' => 'nav']
        ]);

        /** Homepage */
        $menu
            ->addChild('Accueil', ['route' => 'app_home'])
            ->setAttribute('class', 'active');

        /** Menu des catégorie */
        $menu
            ->addChild('Catégorie', ['uri' => '#'])
            ->setChildrenAttribute('class', 'dropdown');

        /** Sous menu des articles */
        $seasons = $this->seasonRepository->findAll();
        foreach ($seasons as $value) {
            $menu['Catégorie']
                ->addChild($value->getTitle(), ['uri' => '#'])
                ->setChildrenAttribute('class', 'dropdown');

            foreach ($value->getCategories() as $article) {
                if ($article->getActive()) {
                    $menu['Catégorie'][$value->getTitle()]->addChild(
                        $article->getTitle(),
                        [
                            'route' => 'app_category',
                            'routeParameters' => [
                                'id' => $article->getId(),
                                'slug' => $this->toolsService->slugify($article->getTitle())
                            ]
                        ]
                    );
                }
            }
        }

        /** Menu complémentaire */
        $menu->addChild('A propos', ['route' => 'app_information']);
        $menu->addChild('Contact', ['route' => 'app_contact']);

        /** Menu de l'utilisateur */
        if ($this->security->getUser() === null) {
            $menu->addChild('Connexion', ['route' => 'app_login']);
        } else {
            $menu
                ->addChild('Mon compte', ['uri' => '#'])
                ->setChildrenAttribute('class', 'dropdown');

            $menu['Mon compte']->addChild('Mon profil', ['route' => 'app_profil']);
            $menu['Mon compte']->addChild('Administration', ['route' => 'admin_home']);
            $menu['Mon compte']->addChild('Déconnexion', ['route' => 'app_logout']);
        }

        return $menu;
    }
}

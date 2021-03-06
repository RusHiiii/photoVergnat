<?php

namespace App\EventListener\WebApp;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Season;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Security;

class CategoryEntityListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * Insertion d'une catégorie
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Category) {
            return;
        }

        $entity->setCreated(new \DateTime('now'));
        $entity->setUpdated(new \DateTime('now'));
    }

    /**
     * MàJ d'une categorie
     * @param PreUpdateEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Category) {
            return;
        }

        $entity->setUpdated(new \DateTime('now'));
    }
}

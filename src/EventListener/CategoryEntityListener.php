<?php

namespace App\EventListener;

use App\Entity\Category;
use App\Entity\Season;
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
        $entity->setUser($this->security->getUser());
    }

    /**
     * MàJ d'une categorie
     * @param LifecycleEventArgs $args
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

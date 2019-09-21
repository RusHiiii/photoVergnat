<?php

namespace App\EventListener;

use App\Entity\Season;
use App\Entity\Tag;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SeasonEntityListener
{
    /**
     * Insertion d'une saison
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Season) {
            return;
        }

        $entity->setCreated(new \DateTime('now'));
        $entity->setUpdated(new \DateTime('now'));
    }

    /**
     * MàJ d'une saison
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Season) {
            return;
        }

        $entity->setUpdated(new \DateTime('now'));
    }
}

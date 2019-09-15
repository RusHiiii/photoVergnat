<?php

namespace App\EventListener;

use App\Entity\Tag;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TagEntityListener
{
    /**
     * Insertion d'un tag
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Tag) {
            return;
        }

        $entity->setCreated(new \DateTime('now'));
        $entity->setUpdated(new \DateTime('now'));
    }

    /**
     * MàJ d'un tag
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Tag) {
            return;
        }

        $entity->setUpdated(new \DateTime('now'));
    }
}

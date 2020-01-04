<?php

namespace App\EventListener\WebApp;

use App\Entity\WebApp\Tag;
use App\Entity\WebApp\Type;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TypeEntityListener
{
    /**
     * Insertion d'un tag
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Type) {
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

        if (!$entity instanceof Type) {
            return;
        }

        $entity->setUpdated(new \DateTime('now'));
    }
}

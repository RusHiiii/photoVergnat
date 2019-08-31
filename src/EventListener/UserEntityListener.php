<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserEntityListener
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    /**
     * Insertion d'un utilisateur
     * @param LifecycleEventArgs $args
     * @return bool|void
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        $password = $this->encoder->encodePassword($entity, $entity->getPassword());
        $entity->setPassword($password);
        $entity->setUpdated(new \DateTime('now'));
    }

    /**
     * MàJ d'un utilisateur
     * @param LifecycleEventArgs $args
     * @return bool|void
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            return;
        }

        $entity->setUpdated(new \DateTime('now'));
    }
}

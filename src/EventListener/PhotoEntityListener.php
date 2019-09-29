<?php

namespace App\EventListener;

use App\Entity\Photo;
use App\Service\Tools\FileUploaderService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoEntityListener
{
    private $uploader;

    public function __construct(FileUploaderService $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Insertion d'une photo
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Photo) {
            return;
        }

        $entity->setCreated(new \DateTime('now'));
        $entity->setUpdated(new \DateTime('now'));

        $this->uploadFile($entity);
    }

    /**
     * Suppression d'une photo
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Photo) {
            return;
        }

        if(file_exists($this->uploader->getTargetDirectory() . $entity->getFile())){
            unlink($this->uploader->getTargetDirectory() . $entity->getFile());
        }
    }

    /**
     * MàJ d'une photo
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Photo) {
            return;
        }

        $entity->setUpdated(new \DateTime('now'));
    }

    /**
     * Upload du fichier
     * @param $entity
     * @throws \Exception
     */
    private function uploadFile($entity)
    {
        $file = $entity->getFile();

        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $entity->setFile($fileName['filename']);
        }
    }
}

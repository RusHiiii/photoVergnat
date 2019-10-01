<?php

namespace App\EventListener;

use App\Entity\Photo;
use App\Service\Tools\FileUploaderService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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

        $this->removeFile($entity->getFile());
    }

    /**
     * MàJ d'une photo
     * @param PreUpdateEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Photo) {
            return;
        }

        $oldFile = $args->getOldValue('file');
        if ($entity->getFile() === null) {
            $entity->setFile($oldFile);
        } else {
            $this->removeFile($oldFile);
            $this->uploadFile($entity);
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

    /**
     * Suppression d'un fichier
     * @param string $filename
     * @return bool
     */
    private function removeFile(string $filename)
    {
        if (file_exists($this->uploader->getTargetDirectory() . $filename)) {
            unlink($this->uploader->getTargetDirectory() . $filename);
            return true;
        }

        return false;
    }
}

<?php

namespace App\EventListener\WebApp;

use App\Entity\WebApp\Photo;
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
        $this->getExifData($entity);
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

        if ($entity->getFile() instanceof UploadedFile) {
            $this->removeFile($args->getOldValue('file'));
            $this->uploadFile($entity);
        }

        if ($entity->getFile() === null) {
            $entity->setFile($args->getOldValue('file'));
        }

        $this->getExifData($entity);
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

    /**
     * Récupération de la photo
     * @param Photo $photo
     */
    private function getExifData(Photo $photo){
        $data = exif_read_data($this->uploader->getTargetDirectory() . $photo->getFile(),0, true);

        $information = 'NC';
        if (isset($data['COMPUTED']['ApertureFNumber'])) {
            $information = sprintf('%s: %ss à %s, %s ISO', $data['IFD0']['Model'], $data['EXIF']['ExposureTime'], $data['COMPUTED']['ApertureFNumber'], $data['EXIF']['ISOSpeedRatings']);
        }

        $photo->setInformation($information);
    }
}

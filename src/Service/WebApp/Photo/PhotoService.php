<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Photo;

use App\Entity\WebApp\Photo;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\WebApp\Photo\Assembler\PhotoAssembler;
use App\Service\WebApp\Photo\Exceptions\PhotoInvalidDataException;
use App\Service\WebApp\Photo\Validator\PhotoValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class PhotoService
{
    private $entityManager;
    private $photoValidatorService;
    private $tagRepository;
    private $typeRepository;
    private $photoRepository;
    private $photoAssembler;

    public function __construct(
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository,
        PhotoValidator $photoValidatorService,
        TypeRepository $typeRepository,
        PhotoRepository $photoRepository,
        PhotoAssembler $photoAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->photoValidatorService = $photoValidatorService;
        $this->tagRepository = $tagRepository;
        $this->typeRepository = $typeRepository;
        $this->photoRepository = $photoRepository;
        $this->photoAssembler = $photoAssembler;
    }

    /**
     * Suppression d'une photo
     * @param Photo $photo
     * @return bool
     */
    public function removePhoto(Photo $photo): bool
    {
        /** Suppression */
        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création de photo
     * @param array $data
     * @param UploadedFile|null $file
     * @return Photo
     * @throws PhotoInvalidDataException
     * @throws \App\Service\WebApp\Tag\Exceptions\TagNotFoundException
     * @throws \App\Service\WebApp\Type\Exceptions\TypeNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createPhoto(array $data, ?UploadedFile $file): Photo
    {
        /** Validation des données */
        $validatedData = $this->photoValidatorService->checkCreatePhoto($data, $file);

        if (count($validatedData['errors']) > 0) {
            throw new PhotoInvalidDataException($validatedData['errors'], PhotoInvalidDataException::PHOTO_INVALID_DATA_MESSAGE);
        }

        /** Insertion de la photo et sauvegarde */
        $photo = $this->photoAssembler->create($validatedData['data'], $file);

        /** Sauvegarde */
        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return $photo;
    }

    /**
     * Edition d'une photo
     * @param array $data
     * @param UploadedFile|null $file
     * @param Photo $photo
     * @return Photo
     * @throws Exceptions\PhotoNotFoundException
     * @throws PhotoInvalidDataException
     * @throws \App\Service\WebApp\Tag\Exceptions\TagNotFoundException
     * @throws \App\Service\WebApp\Type\Exceptions\TypeNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updatePhoto(array $data, ?UploadedFile $file, Photo $photo): Photo
    {
        /** Validation des données */
        $validatedData = $this->photoValidatorService->checkUpdatePhoto($data, $file);
        if (count($validatedData['errors']) > 0) {
            throw new PhotoInvalidDataException($validatedData['errors'], PhotoInvalidDataException::PHOTO_INVALID_DATA_MESSAGE);
        }

        /** MàJ de la photo et sauvegarde */
        $photo = $this->photoAssembler->edit($photo, $validatedData['data'], $file);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $photo;
    }

    /**
     * Récupération des actions
     * @return array
     */
    public function getLastAction(): array
    {
        $data = [];

        /** Récupération des infos */
        $photos = $this->photoRepository->findByLast(5);

        foreach ($photos as $photo) {
            $data[] = [
                'icon' => 'camera',
                'action' => 'photos',
                'title' => $photo->getTitle(),
                'created' => $photo->getCreated(),
                'updated' => $photo->getUpdated()
            ];
        }

        return $data;
    }

    /**
     * Statistique sur le nombre de photo
     * @return array
     * @throws \Exception
     */
    public function getNumberPhotoByMonth()
    {
        $statistics = [];
        $end = new \DateTime();

        $begin = new \DateTime('first day of this month');
        $begin = $begin->modify( '-4 month' );

        $interval = new \DateInterval('P1M');
        $daterange = new \DatePeriod($begin, $interval ,$end);

        foreach($daterange as $date){
            $statistics[] = [
                'month' => $date->format('M'),
                'count' => $this->photoRepository->countByMonth($date)
            ];
        }

        return $statistics;
    }
}

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
use App\Service\WebApp\Photo\Validator\PhotoValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class PhotoService
{
    const MSG_UNKNOWN_PHOTO = 'Photo inexistante !';

    private $entityManager;
    private $security;
    private $photoValidatorService;
    private $serialize;
    private $tagRepository;
    private $typeRepository;
    private $photoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        TagRepository $tagRepository,
        PhotoValidator $photoValidatorService,
        SerializerInterface $serializer,
        TypeRepository $typeRepository,
        PhotoRepository $photoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->photoValidatorService = $photoValidatorService;
        $this->serialize = $serializer;
        $this->tagRepository = $tagRepository;
        $this->typeRepository = $typeRepository;
        $this->photoRepository = $photoRepository;
    }

    /**
     * Création de photo
     * @param array $data
     * @param UploadedFile|null $file
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createPhoto(array $data, ?UploadedFile $file): array
    {
        /** Validation des données */
        $validatedData = $this->photoValidatorService->checkCreatePhoto($data, $file, PhotoValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'photo' => []
            ];
        }

        /** Insertion de la photo et sauvegarde */
        $photo = new Photo();
        $photo->setTitle($validatedData['data']['title']);
        $photo->setType($this->typeRepository->findById($validatedData['data']['format']));
        $photo->setFile($file);
        foreach ($validatedData['data']['tags'] as $tag) {
            $photo->addTag($this->tagRepository->findById($tag));
        }

        /** Sauvegarde */
        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'photo' => $this->serialize->serialize($photo, 'json', ['groups' => ['default', 'photo']])
        ];
    }

    /**
     * MàJ d'une photo
     * @param array $data
     * @param UploadedFile|null $file
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updatePhoto(array $data, ?UploadedFile $file, Photo $photo): array
    {
        /** Validation des données */
        $validatedData = $this->photoValidatorService->checkUpdatePhoto($data, $file, PhotoValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'photo' => []
            ];
        }

        /** MàJ de la photo et sauvegarde */
        $photo->setTitle($validatedData['data']['title']);
        $photo->setType($this->typeRepository->findById($validatedData['data']['format']));
        $photo->setFile($file);
        $photo->resetTags();
        foreach ($validatedData['data']['tags'] as $tag) {
            $photo->addTag($this->tagRepository->findById($tag));
        }

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'photo' => $this->serialize->serialize($photo, 'json', ['groups' => ['default', 'photo']])
        ];
    }

    /**
     * Suppression d'une photo
     * @param string $data
     * @return array
     */
    public function removePhoto(Photo $photo): array
    {
        /** Suppression */
        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
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
}

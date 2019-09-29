<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Photo;


use App\Entity\Photo;
use App\Entity\Tag;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use App\Repository\TypeRepository;
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
        PhotoValidatorService $photoValidatorService,
        SerializerInterface $serializer,
        TypeRepository $typeRepository,
        PhotoRepository $photoRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->photoValidatorService = $photoValidatorService;
        $this->serialize = $serializer;
        $this->tagRepository = $tagRepository;
        $this->typeRepository = $typeRepository;
        $this->photoRepository = $photoRepository;
    }

    public function createPhoto(array $data, ?UploadedFile $file): array
    {
        /** Validation des données */
        $validatedData = $this->photoValidatorService->checkCreatePhoto($data, $file);
        if(count($validatedData['errors']) > 0) {
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

        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'photo' => $this->serialize->serialize($photo, 'json')
        ];
    }

    /**
     * Suppression d'une photo
     * @param string $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removePhoto(string $data): array
    {
        /** On récupére le tag */
        $photo = $this->photoRepository->findById($data);
        if($photo === null) {
            return [
                'errors' => [self::MSG_UNKNOWN_PHOTO]
            ];
        }

        /** Suppression */
        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }
}
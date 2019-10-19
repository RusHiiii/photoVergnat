<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Category;

use App\Entity\Category;
use App\Entity\Photo;
use App\Repository\CategoryRepository;
use App\Repository\PhotoRepository;
use App\Repository\SeasonRepository;
use App\Repository\TagRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryService
{
    const MSG_UNKNOWN_CATEGORY = 'Categorie inexistante !';

    private $entityManager;
    private $security;
    private $categoryValidatorService;
    private $serialize;
    private $tagRepository;
    private $categoryRepository;
    private $seasonRepository;
    private $photoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        TagRepository $tagRepository,
        CategoryValidatorService $categoryValidatorService,
        SerializerInterface $serializer,
        CategoryRepository $categoryRepository,
        SeasonRepository $seasonRepository,
        PhotoRepository $photoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->categoryValidatorService = $categoryValidatorService;
        $this->serialize = $serializer;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->seasonRepository = $seasonRepository;
        $this->photoRepository = $photoRepository;
    }

    /**
     * Suppression de la catégorie
     * @param string $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeCategory(string $data): array
    {
        /** On récupére la categorie */
        $category = $this->categoryRepository->findById($data);
        if ($category === null) {
            return [
                'errors' => [self::MSG_UNKNOWN_CATEGORY]
            ];
        }

        /** Suppression */
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Création d'un catégorie
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createCategory(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->categoryValidatorService->checkCategory($data, CategoryValidatorService::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'category' => []
            ];
        }

        /** Insertion de la category et sauvegarde */
        $category = new Category();
        $category->setTitle($validatedData['data']['title']);
        $category->setDescription($validatedData['data']['description']);
        $category->setCity($validatedData['data']['city']);
        $category->setActive($validatedData['data']['active']);
        $category->setLatitude($validatedData['data']['lat']);
        $category->setLongitude($validatedData['data']['lng']);
        $category->setSeason($this->seasonRepository->findById($validatedData['data']['season']));

        foreach ($validatedData['data']['tags'] as $tag) {
            $category->addTag($this->tagRepository->findById($tag));
        }

        foreach ($validatedData['data']['photos'] as $photo) {
            $category->addPhoto($this->photoRepository->findById($photo));
        }

        /** Sauvegarde */
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'category' => $this->serialize->serialize($category, 'json', ['groups' => ['default', 'category']])
        ];
    }

    /**
     * MàJ d'une catégorie
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateCategory(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->categoryValidatorService->checkCategory($data, CategoryValidatorService::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'category' => []
            ];
        }

        /** MàJ de la category et sauvegarde */
        $category = $this->categoryRepository->findById($validatedData['data']['id']);
        $category->setTitle($validatedData['data']['title']);
        $category->setDescription($validatedData['data']['description']);
        $category->setCity($validatedData['data']['city']);
        $category->setActive($validatedData['data']['active']);
        $category->setLatitude($validatedData['data']['lat']);
        $category->setLongitude($validatedData['data']['lng']);
        $category->setSeason($this->seasonRepository->findById($validatedData['data']['season']));

        $category->resetTags();
        foreach ($validatedData['data']['tags'] as $tag) {
            $category->addTag($this->tagRepository->findById($tag));
        }

        $category->resetPhoto();
        foreach ($validatedData['data']['photos'] as $photo) {
            $category->addPhoto($this->photoRepository->findById($photo));
        }

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'category' => $this->serialize->serialize($category, 'json', ['groups' => ['default', 'category']])
        ];
    }

    /**
     * Filtrage des photos
     * @param array $photos
     * @return array
     */
    public function filterPhotoByType($photos): array
    {
        $filteredPhotos = [];

        foreach ($photos as $photo) {
            $filteredPhotos[strtolower($photo->getType()->getTitle())][] = $photo;
        }

        return $filteredPhotos;
    }
}

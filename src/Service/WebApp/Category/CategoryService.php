<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Category;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Photo;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\WebApp\Category\Assembler\CategoryAssembler;
use App\Service\WebApp\Category\Exceptions\CategoryInvalidDataException;
use App\Service\WebApp\Category\Validator\CategoryValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryService
{
    private $categoryAssembler;
    private $entityManager;
    private $categoryValidatorService;
    private $tagRepository;
    private $categoryRepository;
    private $seasonRepository;
    private $photoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository,
        CategoryValidator $categoryValidatorService,
        CategoryRepository $categoryRepository,
        SeasonRepository $seasonRepository,
        PhotoRepository $photoRepository,
        CategoryAssembler $categoryAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->categoryValidatorService = $categoryValidatorService;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->seasonRepository = $seasonRepository;
        $this->photoRepository = $photoRepository;
        $this->categoryAssembler = $categoryAssembler;
    }

    /**
     * Suppression de la catégorie
     * @param Category $category
     * @return bool
     */
    public function removeCategory(Category $category): bool
    {
        /** Suppression */
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création d'un catégorie
     * @param array $data
     * @return Category
     * @throws \App\Service\WebApp\Photo\Exceptions\PhotoNotFoundException
     * @throws \App\Service\WebApp\Season\Exceptions\SeasonNotFoundException
     * @throws \App\Service\WebApp\Tag\Exceptions\TagNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws CategoryInvalidDataException
     */
    public function createCategory(array $data): Category
    {
        /** Validation des données */
        $validatedData = $this->categoryValidatorService->checkCategory($data, CategoryValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            throw new CategoryInvalidDataException($validatedData['errors'], CategoryInvalidDataException::CATEGORY_INVALID_DATA_MESSAGE);
        }

        /** Insertion de la category et sauvegarde */
        $category = $this->categoryAssembler->create($validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    /**
     * MàJ d'une catégorie
     * @param array $data
     * @param Category $category
     * @return Category
     * @throws CategoryInvalidDataException
     * @throws Exceptions\CategoryNotFoundException
     * @throws \App\Service\WebApp\Photo\Exceptions\PhotoNotFoundException
     * @throws \App\Service\WebApp\Season\Exceptions\SeasonNotFoundException
     * @throws \App\Service\WebApp\Tag\Exceptions\TagNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateCategory(array $data, Category $category): Category
    {
        /** Validation des données */
        $validatedData = $this->categoryValidatorService->checkCategory($data, CategoryValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            throw new CategoryInvalidDataException($validatedData['errors'], CategoryInvalidDataException::CATEGORY_INVALID_DATA_MESSAGE);
        }

        /** MàJ de la category et sauvegarde */
        $category = $this->categoryAssembler->edit($category, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $category;
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

    /**
     * Récupération des actions
     * @return array
     */
    public function getLastAction(): array
    {
        $data = [];

        /** Récupération des infos */
        $categories = $this->categoryRepository->findByLast(5);

        foreach ($categories as $category) {
            $data[] = [
                'icon' => 'file-text',
                'action' => 'categories',
                'title' => $category->getTitle(),
                'created' => $category->getCreated(),
                'updated' => $category->getUpdated()
            ];
        }

        return $data;
    }
}

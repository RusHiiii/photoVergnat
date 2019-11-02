<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Category\Assembler;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Type;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException as NotFoundException;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException;
use App\Service\WebApp\Photo\Exceptions\PhotoNotFoundException;
use App\Service\WebApp\Season\Exceptions\SeasonNotFoundException;
use App\Service\WebApp\Tag\Exceptions\TagNotFoundException;

class CategoryAssembler
{
    private $seasonRepository;
    private $tagRepository;
    private $photoRepository;

    public function __construct(
        SeasonRepository $seasonRepository,
        TagRepository $tagRepository,
        PhotoRepository $photoRepository
    ) {
        $this->tagRepository = $tagRepository;
        $this->seasonRepository = $seasonRepository;
        $this->photoRepository = $photoRepository;
    }

    /**
     * Création d'une catégorie
     * @param array $data
     * @return Category
     * @throws PhotoNotFoundException
     * @throws SeasonNotFoundException
     * @throws TagNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(array $data)
    {
        $category = new Category();
        $category->setTitle($data['title']);
        $category->setDescription($data['data']['description']);
        $category->setCity($data['data']['city']);
        $category->setActive($data['data']['active']);
        $category->setLatitude($data['data']['lat']);
        $category->setLongitude($data['data']['lng']);

        $season = $this->seasonRepository->findById($data['season']);
        if ($season === null) {
            throw new SeasonNotFoundException(['Saison inexistante'], SeasonNotFoundException::SEASON_NOT_FOUND);
        }
        $category->setSeason($season);

        foreach ($data['tags'] as $tag) {
            $tag = $this->tagRepository->findById($tag);
            if ($tag === null) {
                throw new TagNotFoundException(['Tag inexistant'], TagNotFoundException::TAG_NOT_FOUND_MESSAGE);
            }
            $category->addTag($tag);
        }

        foreach ($data['photos'] as $photo) {
            $photo = $this->photoRepository->findById($photo);
            if ($photo === null) {
                throw new PhotoNotFoundException(['Photo inexistante'], PhotoNotFoundException::PHOTO_NOT_FOUND_MESSAGE);
            }
            $category->addPhoto($photo);
        }

        return $category;
    }

    /**
     * Edition d'une catégorie
     * @param Category $category
     * @param array $data
     * @return Category
     * @throws CategoryNotFoundException
     * @throws PhotoNotFoundException
     * @throws SeasonNotFoundException
     * @throws TagNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Category $category, array $data)
    {
        if ($category === null) {
            throw new CategoryNotFoundException(['Catégorie inexistante'], CategoryNotFoundException::CATEGORY_NOT_FOUND_MESSAGE);
        }

        $category->setTitle($data['title']);
        $category->setDescription($data['description']);
        $category->setCity($data['city']);
        $category->setActive($data['active']);
        $category->setLatitude($data['lat']);
        $category->setLongitude($data['lng']);

        $season = $this->seasonRepository->findById($data['season']);
        if ($season === null) {
            throw new SeasonNotFoundException(['Saison inexistante'], SeasonNotFoundException::SEASON_NOT_FOUND);
        }
        $category->setSeason($season);

        $category->resetTags();
        foreach ($data['tags'] as $tag) {
            $tag = $this->tagRepository->findById($tag);
            if ($tag === null) {
                throw new TagNotFoundException(['Tag inexistant'], TagNotFoundException::TAG_NOT_FOUND_MESSAGE);
            }
            $category->addTag($tag);
        }

        $category->resetPhoto();
        foreach ($data['photos'] as $photo) {
            $photo = $this->photoRepository->findById($photo);
            if ($photo === null) {
                throw new PhotoNotFoundException(['Photo inexistante'], PhotoNotFoundException::PHOTO_NOT_FOUND_MESSAGE);
            }
            $category->addPhoto($photo);
        }

        return $category;
    }
}

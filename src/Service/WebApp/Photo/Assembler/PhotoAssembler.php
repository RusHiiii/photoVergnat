<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Photo\Assembler;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Photo;
use App\Entity\WebApp\Type;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Repository\WebApp\Photo\Doctrine\PhotoRepository;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException as NotFoundException;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException;
use App\Service\WebApp\Photo\Exceptions\PhotoNotFoundException;
use App\Service\WebApp\Season\Exceptions\SeasonNotFoundException;
use App\Service\WebApp\Tag\Exceptions\TagNotFoundException;
use App\Service\WebApp\Type\Exceptions\TypeNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoAssembler
{
    private $tagRepository;
    private $typeRepository;

    public function __construct(
        TagRepository $tagRepository,
        TypeRepository $typeRepository
    ) {
        $this->tagRepository = $tagRepository;
        $this->typeRepository = $typeRepository;
    }

    /**
     * Création d'une photo
     * @param array $data
     * @param UploadedFile $file
     * @return Photo
     * @throws TagNotFoundException
     * @throws TypeNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(array $data, UploadedFile $file)
    {
        $photo = new Photo();
        $photo->setTitle($data['title']);
        $photo->setFile($file);

        $type = $this->typeRepository->findById($data['format']);
        if ($type === null) {
            throw new TypeNotFoundException(['Type inexistant'], TypeNotFoundException::TYPE_NOT_FOUND_MESSAGE);
        }
        $photo->setType($type);

        foreach ($data['tags'] as $tag) {
            $tag = $this->tagRepository->findById($tag);
            if ($tag === null) {
                throw new TagNotFoundException(['Tag inexistant'], TagNotFoundException::TAG_NOT_FOUND_MESSAGE);
            }
            $photo->addTag($tag);
        }

        return $photo;
    }

    /**
     * Edition d'une photo
     * @param Photo $photo
     * @param array $data
     * @param UploadedFile $file
     * @return Photo
     * @throws PhotoNotFoundException
     * @throws TagNotFoundException
     * @throws TypeNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Photo $photo, array $data, ?UploadedFile $file)
    {
        if ($photo === null) {
            throw new PhotoNotFoundException(['Photo inexistante'], PhotoNotFoundException::PHOTO_NOT_FOUND_MESSAGE);
        }

        $photo->setTitle($data['title']);
        $photo->setFile($file);

        $type = $this->typeRepository->findById($data['format']);
        if ($type === null) {
            throw new TypeNotFoundException(['Type inexistant'], TypeNotFoundException::TYPE_NOT_FOUND_MESSAGE);
        }
        $photo->setType($type);

        $photo->resetTags();
        foreach ($data['tags'] as $tag) {
            $tag = $this->tagRepository->findById($tag);
            if ($tag === null) {
                throw new TagNotFoundException(['Tag inexistant'], TagNotFoundException::TAG_NOT_FOUND_MESSAGE);
            }
            $photo->addTag($tag);
        }

        return $photo;
    }
}

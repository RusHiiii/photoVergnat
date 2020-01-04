<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Tag\Assembler;

use App\Entity\WebApp\Tag;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\WebApp\Tag\Exceptions\TagNotFoundException;

class TagAssembler
{
    private $tagRepository;

    public function __construct(
        TagRepository $tagRepository
    ) {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Création d'un tag
     * @param array $data
     * @return Tag
     */
    public function create(array $data)
    {
        $tag = new Tag();

        $tag->setTitle($data['title']);
        $tag->setType($data['type']);

        return $tag;
    }

    /**
     * Edition d'un tag
     * @param Tag $type
     * @param array $data
     * @return Tag
     * @throws TagNotFoundException
     */
    public function edit(Tag $type, array $data)
    {
        if ($type == null) {
            throw new TagNotFoundException(['Tag inexistant'], TagNotFoundException::TAG_NOT_FOUND_MESSAGE);
        }

        $type->setTitle($data['title']);
        $type->setType($data['type']);

        return $type;
    }
}

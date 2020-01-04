<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Comment\Assembler;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Comment;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Service\WebApp\Category\Exceptions\CategoryNotFoundException;

class CommentAssembler
{
    private $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Création d'un commentaire
     * @param array $data
     * @return Category|Comment|null
     * @throws CategoryNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(array $data)
    {
        $comment = new Comment();
        $comment->setEmail($data['email']);
        $comment->setName($data['name']);
        $comment->setMessage($data['message']);

        $category = $this->categoryRepository->findById($data['category']);
        if ($category == null) {
            throw new CategoryNotFoundException(['Article inexistant'], CategoryNotFoundException::CATEGORY_NOT_FOUND_MESSAGE);
        }
        $comment->setCategory($category);

        return $comment;
    }

    /**
     * Edition d'un commentaire
     * @param Comment $comment
     * @param array $data
     * @return Comment
     * @throws CategoryNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Comment $comment, array $data)
    {
        $comment->setEmail($data['email']);
        $comment->setName($data['name']);
        $comment->setMessage($data['message']);

        $category = $this->categoryRepository->findById($data['category']);
        if ($category == null) {
            throw new CategoryNotFoundException(['Article inexistant'], CategoryNotFoundException::CATEGORY_NOT_FOUND_MESSAGE);
        }
        $comment->setCategory($category);

        return $comment;
    }
}

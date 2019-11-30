<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Comment;

use App\Entity\WebApp\Comment;
use App\Repository\WebApp\Category\Doctrine\CategoryRepository;
use App\Service\WebApp\Comment\Assembler\CommentAssembler;
use App\Service\WebApp\Comment\Exceptions\CommentInvalidDataException;
use App\Service\WebApp\Comment\Validator\CommentValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class CommentService
{
    private $entityManager;
    private $categoryRepository;
    private $messageValidatorService;
    private $commentAssembler;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommentValidator $messageValidatorService,
        CategoryRepository $categoryRepository,
        CommentAssembler $commentAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->messageValidatorService = $messageValidatorService;
        $this->commentAssembler = $commentAssembler;
    }

    /**
     * Suppression d'un commentaire
     * @param Comment $comment
     * @return bool
     */
    public function removeComment(Comment $comment): bool
    {
        /** Suppression */
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création d'un commentaire
     * @param array $data
     * @return Comment
     * @throws CommentInvalidDataException
     * @throws \App\Service\WebApp\Category\Exceptions\CategoryNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createComment(array $data): Comment
    {
        /** Validation des données */
        $validatedData = $this->messageValidatorService->checkComment($data, CommentValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            throw new CommentInvalidDataException($validatedData['errors'], CommentInvalidDataException::COMMENT_INVALID_DATA_MESSAGE);
        }

        /** Insertion du commentaire et sauvegarde */
        $comment = $this->commentAssembler->create($validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    /**
     * Edition d'un commentaire
     * @param array $data
     * @param Comment $comment
     * @return Comment
     * @throws CommentInvalidDataException
     * @throws \App\Service\WebApp\Category\Exceptions\CategoryNotFoundException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateComment(array $data, Comment $comment): Comment
    {
        /** Validation des données */
        $validatedData = $this->messageValidatorService->checkComment($data, CommentValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            throw new CommentInvalidDataException($validatedData['errors'], CommentInvalidDataException::COMMENT_INVALID_DATA_MESSAGE);
        }

        /** MàJ de la saison et sauvegarde */
        $season = $this->commentAssembler->edit($comment, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $season;
    }
}

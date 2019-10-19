<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class CommentService
{
    const MSG_UNKNOWN_MESSAGE = 'Message inexistant !';

    private $entityManager;
    private $categoryRepository;
    private $messageValidatorService;
    private $serialize;

    public function __construct(
        EntityManagerInterface $entityManager,
        CommentValidatorService $messageValidatorService,
        CategoryRepository $categoryRepository,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->messageValidatorService = $messageValidatorService;
        $this->serialize = $serializer;
    }

    /**
     * Création d'un commentaire
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createComment(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->messageValidatorService->checkComment($data, CommentValidatorService::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'comment' => []
            ];
        }

        /** Insertion du commentaire et sauvegarde */
        $comment = new Comment();
        $comment->setEmail($validatedData['data']['email']);
        $comment->setName($validatedData['data']['name']);
        $comment->setCategory($this->categoryRepository->findById($validatedData['data']['category']));
        $comment->setMessage($validatedData['data']['message']);

        /** Sauvegarde */
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'comment' => $this->serialize->serialize($comment, 'json', ['groups' => ['default']])
        ];
    }
}

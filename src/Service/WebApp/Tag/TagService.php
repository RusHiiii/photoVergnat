<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Tag;

use App\Entity\WebApp\Tag;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\WebApp\Tag\Assembler\TagAssembler;
use App\Service\WebApp\Tag\Exceptions\InvalidDataException;
use App\Service\WebApp\Tag\Validator\TagValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class TagService
{
    const INVALID_UPDATE = 'Données de mise à jour du tag invalide';
    const INVALID_CREATE = 'Données de création du tag invalide';

    private $entityManager;
    private $tagValidatorService;
    private $tagRepository;
    private $tagAssembler;

    public function __construct(
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository,
        TagValidator $tagValidatorService,
        TagAssembler $tagAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->tagValidatorService = $tagValidatorService;
        $this->tagRepository = $tagRepository;
        $this->tagAssembler = $tagAssembler;
    }

    /**
     * Suppression du tag
     * @param Tag $tag
     * @return bool
     */
    public function removeTag(Tag $tag): bool
    {
        /** Suppression */
        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création d'un tag
     * @param array $data
     * @return Tag
     * @throws InvalidDataException
     */
    public function createTag(array $data): Tag
    {
        /** Validation des données */
        $validatedData = $this->tagValidatorService->checkTag($data, TagValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            throw new InvalidDataException($validatedData['errors'], self::INVALID_CREATE);
        }

        /** Insertion du tag et sauvegarde */
        $tag = $this->tagAssembler->create($validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $tag;
    }

    /**
     * MàJ d'un tag
     * @param array $data
     * @param Tag $tag
     * @return Tag
     * @throws Exceptions\NotFoundException
     * @throws InvalidDataException
     */
    public function updateTag(array $data, Tag $tag): Tag
    {
        /** Validation des données */
        $validatedData = $this->tagValidatorService->checkTag($data, TagValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            throw new InvalidDataException($validatedData['errors'], self::INVALID_UPDATE);
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $tag = $this->tagAssembler->edit($tag, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $tag;
    }

    /**
     * Récupération des actions
     * @return array
     */
    public function getLastAction(): array
    {
        $data = [];

        /** Récupération des infos */
        $tags = $this->tagRepository->findByLast(5);

        foreach ($tags as $tag) {
            $data[] = [
                'icon' => 'tag',
                'action' => 'tags',
                'title' => $tag->getTitle(),
                'created' => $tag->getCreated(),
                'updated' => $tag->getUpdated()
            ];
        }

        return $data;
    }
}

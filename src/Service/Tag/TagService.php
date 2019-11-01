<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Tag;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class TagService
{
    const MSG_UNKNOWN_TAG = 'Tag inexistant !';

    private $entityManager;
    private $security;
    private $tagValidatorService;
    private $serialize;
    private $tagRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        TagRepository $tagRepository,
        TagValidatorService $tagValidatorService,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->tagValidatorService = $tagValidatorService;
        $this->serialize = $serializer;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Suppression du tag
     * @param string $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeTag(Tag $tag): array
    {
        /** Suppression */
        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Création d'un tag
     * @param array $data
     * @return array
     */
    public function createTag(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->tagValidatorService->checkTag($data, TagValidatorService::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'tag' => []
            ];
        }

        /** Insertion du tag et sauvegarde */
        $tag = new Tag();
        $tag->setTitle($validatedData['data']['title']);
        $tag->setType($validatedData['data']['type']);

        /** Sauvegarde */
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'tag' => $this->serialize->serialize($tag, 'json', ['groups' => ['default', 'tag']])
        ];
    }

    /**
     * MàJ d'un tag
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateTag(array $data, Tag $tag): array
    {
        /** Validation des données */
        $validatedData = $this->tagValidatorService->checkTag($data, TagValidatorService::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'tag' => []
            ];
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $tag->setTitle($validatedData['data']['title']);
        $tag->setType($validatedData['data']['type']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'tag' => $this->serialize->serialize($tag, 'json', ['groups' => ['default', 'tag']])
        ];
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

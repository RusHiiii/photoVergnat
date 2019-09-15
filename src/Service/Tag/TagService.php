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
    )
    {
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
    public function removeTag(string $data): array
    {
        /** On récupére le tag */
        $tag = $this->tagRepository->findById($data);
        if($tag === null) {
            return [
                'errors' => [self::MSG_UNKNOWN_TAG]
            ];
        }

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
        $validatedData = $this->tagValidatorService->checkCreateTag($data);
        if(count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'tag' => []
            ];
        }

        /** Insertion du tag et sauvegarde */
        $tag = new Tag();
        $tag->setTitle($validatedData['data']['title']);
        $tag->setType($validatedData['data']['type']);

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'tag' => $this->serialize->serialize($tag, 'json')
        ];
    }

    /**
     * MàJ d'un tag
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateTag(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->tagValidatorService->checkUpdateTag($data);
        if(count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'tag' => []
            ];
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $tag = $this->tagRepository->findById($validatedData['data']['id']);

        $tag->setTitle($validatedData['data']['title']);
        $tag->setType($validatedData['data']['type']);

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'tag' => $this->serialize->serialize($tag, 'json')
        ];
    }
}
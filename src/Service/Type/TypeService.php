<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Type;


use App\Entity\Tag;
use App\Entity\Type;
use App\Repository\TagRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class TypeService
{
    const MSG_UNKNOWN_TYPE = 'Type inexistant !';

    private $entityManager;
    private $security;
    private $serialize;
    private $typeRepository;
    private $typeValidatorService;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        TypeRepository $typeRepository,
        SerializerInterface $serializer,
        TypeValidatorService $typeValidatorService
    )
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->serialize = $serializer;
        $this->typeRepository = $typeRepository;
        $this->typeValidatorService = $typeValidatorService;
    }

    /**
     * Suppression d'un type
     * @param string $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeType(string $data): array
    {
        /** On récupére le type */
        $type = $this->typeRepository->findById($data);
        if($type === null) {
            return [
                'errors' => [self::MSG_UNKNOWN_TYPE]
            ];
        }

        /** Suppression */
        $this->entityManager->remove($type);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Création d'un type
     * @param array $data
     * @return array
     */
    public function createType(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->typeValidatorService->checkCreateType($data);
        if(count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'type' => []
            ];
        }

        /** Insertion du type et sauvegarde */
        $type = new Type();
        $type->setTitle($validatedData['data']['title']);

        /** Sauvegarde */
        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'type' => $this->serialize->serialize($type, 'json')
        ];
    }

    /**
     * MàJ d'un type
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateType(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->typeValidatorService->checkUpdateType($data);
        if(count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'type' => []
            ];
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $type = $this->typeRepository->findById($validatedData['data']['id']);
        $type->setTitle($validatedData['data']['title']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'type' => $this->serialize->serialize($type, 'json')
        ];
    }
}
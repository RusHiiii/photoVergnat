<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Type;

use App\Entity\WebApp\Tag;
use App\Entity\WebApp\Type;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\WebApp\Type\Validator\TypeValidator;
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
        TypeValidator $typeValidatorService
    ) {
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
     */
    public function removeType(Type $type): array
    {
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
        $validatedData = $this->typeValidatorService->checkType($data, TypeValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
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
            'type' => $this->serialize->serialize($type, 'json', ['groups' => ['default', 'type']])
        ];
    }

    /**
     * MàJ d'un type
     * @param array $data
     * @return array
     */
    public function updateType(array $data, Type $type): array
    {
        /** Validation des données */
        $validatedData = $this->typeValidatorService->checkType($data, TypeValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'type' => []
            ];
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $type->setTitle($validatedData['data']['title']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'type' => $this->serialize->serialize($type, 'json', ['groups' => ['default', 'type']])
        ];
    }
}

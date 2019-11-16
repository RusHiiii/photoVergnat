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
use App\Service\WebApp\Type\Assembler\TypeAssembler;
use App\Service\WebApp\Type\Exceptions\TypeInvalidDataException;
use App\Service\WebApp\Type\Validator\TypeValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class TypeService
{
    private $entityManager;
    private $typeRepository;
    private $typeValidatorService;
    private $assembler;

    public function __construct(
        EntityManagerInterface $entityManager,
        TypeRepository $typeRepository,
        TypeValidator $typeValidatorService,
        TypeAssembler $assembler
    ) {
        $this->entityManager = $entityManager;
        $this->typeRepository = $typeRepository;
        $this->typeValidatorService = $typeValidatorService;
        $this->assembler = $assembler;
    }

    /**
     * Suppression d'un type
     * @param string $data
     * @return array
     */
    public function removeType(Type $type): bool
    {
        /** Suppression */
        $this->entityManager->remove($type);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création d'un type
     * @param array $data
     * @return array
     * @throws TypeInvalidDataException
     */
    public function createType(array $data): Type
    {
        /** Validation des données */
        $validatedData = $this->typeValidatorService->checkType($data, TypeValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            throw new TypeInvalidDataException($validatedData['errors'], TypeInvalidDataException::TYPE_INVALID_DATA_MESSAGE);
        }

        /** Insertion du type et sauvegarde */
        $type = $this->assembler->create($validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type;
    }

    /**
     * MàJ d'un type
     * @param array $data
     * @param Type $type
     * @return array
     * @throws Exceptions\UserNotFoundException
     * @throws TypeInvalidDataException
     */
    public function updateType(array $data, Type $type): Type
    {
        /** Validation des données */
        $validatedData = $this->typeValidatorService->checkType($data, TypeValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            throw new TypeInvalidDataException($validatedData['errors'], TypeInvalidDataException::TYPE_INVALID_DATA_MESSAGE);
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $type = $this->assembler->edit($type, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $type;
    }
}

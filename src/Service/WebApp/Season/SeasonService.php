<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Season;

use App\Entity\WebApp\Season;
use App\Entity\WebApp\Tag;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Repository\WebApp\Tag\Doctrine\TagRepository;
use App\Service\WebApp\Season\Assembler\SeasonAssembler;
use App\Service\WebApp\Season\Exceptions\InvalidDataException;
use App\Service\WebApp\Season\Validator\SeasonValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class SeasonService
{
    const INVALID_UPDATE = 'Données de mise à jour de la saison invalide';
    const INVALID_CREATE = 'Données de création de la saison invalide';

    private $entityManager;
    private $seasonValidatorService;
    private $seasonRepository;
    private $seasonAssembler;

    public function __construct(
        EntityManagerInterface $entityManager,
        SeasonRepository $seasonRepository,
        SeasonValidator $tagValidatorService,
        SeasonAssembler $seasonAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->seasonValidatorService = $tagValidatorService;
        $this->seasonRepository = $seasonRepository;
        $this->seasonAssembler = $seasonAssembler;
    }

    /**
     * Suppression d'une saison
     * @param Season $season
     * @return bool
     */
    public function removeSeason(Season $season): bool
    {
        /** Suppression */
        $this->entityManager->remove($season);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création d'une saison
     * @param array $data
     * @return Season
     * @throws InvalidDataException
     */
    public function createSeason(array $data): Season
    {
        /** Validation des données */
        $validatedData = $this->seasonValidatorService->checkSeason($data, SeasonValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            throw new InvalidDataException($validatedData['errors'], self::INVALID_CREATE);
        }

        /** Insertion de la saison et sauvegarde */
        $season = $this->seasonAssembler->create($validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $season;
    }

    /**
     * MàJ d'une saison
     * @param array $data
     * @param Season $season
     * @return Season
     * @throws Exceptions\NotFoundException
     * @throws InvalidDataException
     */
    public function updateSeason(array $data, Season $season): Season
    {
        /** Validation des données */
        $validatedData = $this->seasonValidatorService->checkSeason($data, SeasonValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            throw new InvalidDataException($validatedData['errors'], self::INVALID_UPDATE);
        }

        /** MàJ de la saison et sauvegarde */
        $season = $this->seasonAssembler->edit($season, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $season;
    }
}

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
use App\Service\WebApp\Season\Validator\SeasonValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class SeasonService
{
    const MSG_UNKNOWN_SEASON = 'Saison inexistant !';

    private $entityManager;
    private $security;
    private $seasonValidatorService;
    private $serialize;
    private $seasonRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        SeasonRepository $seasonRepository,
        SeasonValidator $tagValidatorService,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->seasonValidatorService = $tagValidatorService;
        $this->serialize = $serializer;
        $this->seasonRepository = $seasonRepository;
    }

    /**
     * Suppression d'une saison
     * @param string $data
     * @return array
     */
    public function removeSeason(Season $season): array
    {
        /** Suppression */
        $this->entityManager->remove($season);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Création d'une saison
     * @param array $data
     * @return array
     */
    public function createSeason(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->seasonValidatorService->checkSeason($data, SeasonValidator::TOKEN_CREATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'season' => []
            ];
        }

        /** Insertion de la saison et sauvegarde */
        $season = new Season();
        $season->setTitle($validatedData['data']['title']);

        /** Sauvegarde */
        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'season' => $this->serialize->serialize($season, 'json', ['groups' => ['default', 'season']])
        ];
    }

    /**
     * MàJ d'une saison
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateSeason(array $data, Season $season): array
    {
        /** Validation des données */
        $validatedData = $this->seasonValidatorService->checkSeason($data, SeasonValidator::TOKEN_UPDATE);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'season' => []
            ];
        }

        /** MàJ de la saison et sauvegarde */
        $season->setTitle($validatedData['data']['title']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'season' => $this->serialize->serialize($season, 'json', ['groups' => ['default', 'season']])
        ];
    }
}

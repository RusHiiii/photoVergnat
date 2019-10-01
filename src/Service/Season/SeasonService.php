<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Season;

use App\Entity\Season;
use App\Entity\Tag;
use App\Repository\SeasonRepository;
use App\Repository\TagRepository;
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
        SeasonValidatorService $tagValidatorService,
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeSeason(string $data): array
    {
        /** On récupére la saison */
        $season = $this->seasonRepository->findById($data);
        if ($season === null) {
            return [
                'errors' => [self::MSG_UNKNOWN_SEASON]
            ];
        }

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
        $validatedData = $this->seasonValidatorService->checkCreateSeason($data);
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
            'season' => $this->serialize->serialize($season, 'json')
        ];
    }

    /**
     * MàJ d'une saison
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateSeason(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->seasonValidatorService->checkUpdateSeason($data);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'season' => []
            ];
        }

        /** MàJ de la saison et sauvegarde */
        $season = $this->seasonRepository->findById($validatedData['data']['id']);
        $season->setTitle($validatedData['data']['title']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'season' => $this->serialize->serialize($season, 'json')
        ];
    }
}

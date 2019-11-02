<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 02/11/2019
 * Time: 15:14
 */

namespace App\Service\WebApp\Season\Assembler;


use App\Entity\WebApp\Season;
use App\Repository\WebApp\Season\Doctrine\SeasonRepository;
use App\Service\WebApp\Season\Exceptions\SeasonNotFoundException;

class SeasonAssembler
{
    private $seasonRepository;

    public function __construct(
        SeasonRepository $seasonRepository
    ) {
        $this->seasonRepository = $seasonRepository;
    }

    /**
     * Création d'une saison
     * @param array $data
     * @return Season
     */
    public function create(array $data)
    {
        $season = new Season();

        $season->setTitle($data['title']);

        return $season;
    }

    /**
     * MàJ d'une saison
     * @param Season $season
     * @param array $data
     * @return Season
     * @throws SeasonNotFoundException
     */
    public function edit(Season $season, array $data)
    {
        if ($season === null) {
            throw new SeasonNotFoundException(['Saison inexistant'], SeasonNotFoundException::SEASON_NOT_FOUND);
        }

        $season->setTitle($data['title']);

        return $season;
    }
}
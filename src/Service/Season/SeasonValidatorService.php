<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Season;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Tools\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class SeasonValidatorService
{
    private $validatorService;
    private $toolsService;

    public function __construct(
        DataValidatorService $dataValidatorService,
        ToolsService $toolsService
    ) {
        $this->validatorService = $dataValidatorService;
        $this->toolsService = $toolsService;
    }

    /**
     * Validation de la données pour la création
     * @param array $data
     * @return array
     */
    public function checkCreateSeason(array $data): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], 'create-season');
        $this->validatorService->validateNotBlank($data['title'], 'Titre');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Validation de la données pour la MàJ
     * @param array $data
     * @return array
     */
    public function checkUpdateSeason(array $data): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], 'update-season');
        $this->validatorService->validateNotBlank($data['title'], 'Titre');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}

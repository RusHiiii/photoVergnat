<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Season\Validator;

use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\Validator\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class SeasonValidator
{
    const TOKEN_UPDATE = 'update-season';
    const TOKEN_CREATE = 'create-season';

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
    public function checkSeason(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateNotBlank($data['title'], 'Titre');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();

        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Information;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Tools\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class InformationValidatorService
{
    const TOKEN_SEND_MAIL = 'send-mail';

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
     * Vérification des données de contact
     * @param array $data
     * @param string $token
     * @return array
     */
    public function checkContact(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateNotBlank($data['subject'], 'Sujet');
        $this->validatorService->validateExist($data, 'choice', 'Sujet');
        $this->validatorService->validateNotBlank($data['message'], 'Message');
        $this->validatorService->validateEmail($data['email'], 'Email');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}

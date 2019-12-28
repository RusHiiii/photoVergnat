<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Information\Validator;

use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\Validator\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class InformationValidator
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
     * Vérification des données de contact
     * @param array $data
     * @param string $token
     * @return array
     */
    public function checkContact(array $data): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
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

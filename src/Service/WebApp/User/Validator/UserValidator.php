<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\User\Validator;

use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserValidator
{
    const TOKEN_UPDATE_USER = 'update-user';
    const TOKEN_UPDATE_PSWD = 'update-password';
    const TOKEN_CREATE_USER = 'create-user';

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
     * Validation de la données pour la mise à jour
     * @param array $data
     * @return array
     */
    public function checkUpdateProfile(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateEmail($data['email'], 'mail');
        $this->validatorService->validateNotBlank($data['lastname'], 'nom');
        $this->validatorService->validateNotBlank($data['firstname'], 'prénom');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Validation de la données pour la création
     * @param array $data
     * @return array
     */
    public function checkCreateUser(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateEmail($data['email'], 'mail');
        $this->validatorService->validateNotBlank($data['lastname'], 'nom');
        $this->validatorService->validateNotBlank($data['firstname'], 'prénom');
        $this->validatorService->validateEqualTo($data['password_first'], $data['password_second'], 'mot de passe');
        $this->validatorService->validateRegex($data['password_first'], 'mot de passe');
        $this->validatorService->validateExist($data, 'roles', 'roles');

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
    public function checkUpdateUser(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateEmail($data['email'], 'mail');
        $this->validatorService->validateNotBlank($data['lastname'], 'nom');
        $this->validatorService->validateNotBlank($data['firstname'], 'prénom');
        $this->validatorService->validateExist($data, 'roles', 'roles');
        $this->validatorService->validateNotBlank($data['created'], 'date');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Validation du mot de passe
     * @param array $data
     * @return array
     */
    public function checkUpdatePassword(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateNotBlank($data['password_first'], 'mot de passe');
        $this->validatorService->validateEqualTo($data['password_first'], $data['password_second'], 'mot de passe');
        $this->validatorService->validateRegex($data['password_first'], 'mot de passe');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Tools;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class DataValidatorService
{
    const MSG_ERROR_EMAIL = 'Le champs « %s » est invalide';
    const MSG_ERROR_BLANK = 'Le champs « %s » est vide';
    const MSG_ERROR_TOKEN = 'Token invalide';

    private $validator;
    private $token;
    private $errors = [];

    public function __construct(
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $this->validator = $validator;
        $this->token = $csrfTokenManager;
    }

    /**
     * Validation de l'email
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email, string $key): bool
    {
        $emailConstraint = new Assert\Email();
        $emailConstraint->message = self::MSG_ERROR_EMAIL;

        $errors = $this->validator->validate(
            $email,
            $emailConstraint
        );

        if (count($errors) > 0) {
            $this->errors[]= sprintf($errors[0]->getMessage(), $key);
            return false;
        }

        return true;
    }

    /**
     * Valdation de champs non vide
     * @param string $blank
     * @param string $key
     * @return bool
     */
    public function validateNotBlank(string $blank, string $key): bool
    {
        $notBlank = new Assert\NotBlank();
        $notBlank->message = self::MSG_ERROR_BLANK;

        $errors = $this->validator->validate(
            $blank,
            $notBlank
        );

        if(count($errors) > 0) {
            $this->errors[] = sprintf($errors[0]->getMessage(), $key);
            return false;
        }

        return true;
    }

    /**
     * Validation du token
     * @param string $token
     * @param string $key
     * @return bool
     */
    public function validateCsrfToken(string $token, string $key): bool
    {
        if (!$this->token->isTokenValid(new CsrfToken($key, $token))) {
            $this->errors[] = self::MSG_ERROR_TOKEN;
            return false;
        }

        return true;
    }

    /**
     * Récupération des erreurs
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
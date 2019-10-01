<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Tools;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class DataValidatorService
{
    const MSG_ERROR_EMAIL = 'Le champs « %s » est invalide';
    const MSG_ERROR_BLANK = 'Le champs « %s » est vide';
    const MSG_ERROR_NULL = 'Le champs « %s » est obligatoire';
    const MSG_ERROR_TOKEN = 'Token invalide';
    const MSG_ERROR_EQUAL = 'Les champs « %s » ne sont pas identique';
    const MSG_ERROR_REGEX = 'Le champs « %s » n\'a pas le bon pattern';
    const MSG_ERROR_EMPTY = 'Le champs « %s » ne peux pas être vide';
    const MSG_ERROR_IMAGE = 'Le fichier « %s » n\'est pas valide';

    private $validator;
    private $token;
    private $errors = [];

    public function __construct(
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
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

        if (count($errors) > 0) {
            $this->errors[] = sprintf($errors[0]->getMessage(), $key);
            return false;
        }

        return true;
    }

    /**
     * Validation du champs non null
     * @param $value
     * @param string $key
     * @return bool
     */
    public function validateNotNull($value, string $key): bool
    {
        $notNull = new Assert\NotNull();
        $notNull->message = self::MSG_ERROR_NULL;

        $errors = $this->validator->validate(
            $value,
            $notNull
        );

        if (count($errors) > 0) {
            $this->errors[] = sprintf($errors[0]->getMessage(), $key);
            return false;
        }

        return true;
    }

    /**
     * Validation valeur identique
     * @param string $first
     * @param string $second
     * @param string $key
     * @return bool
     */
    public function validateEqualTo(string $first, string $second, string $key): bool
    {
        $equalTo = new Assert\EqualTo(['value' => $first]);
        $equalTo->message = self::MSG_ERROR_EQUAL;

        $errors = $this->validator->validate(
            $second,
            $equalTo
        );

        if (count($errors) > 0) {
            $this->errors[] = sprintf($errors[0]->getMessage(), $key);
            return false;
        }

        return true;
    }

    /**
     * Validation de l'image
     * @param UploadedFile $file
     * @param string $key
     * @return bool
     */
    public function validateImage(?UploadedFile $file, string $key): bool
    {
        $image = new Assert\Image();
        $image->mimeTypesMessage = self::MSG_ERROR_IMAGE;
        $image->maxSize = '30M';

        $errors = $this->validator->validate(
            $file,
            $image
        );

        if (count($errors) > 0) {
            $this->errors[] = sprintf($errors[0]->getMessage(), $key);
            return false;
        }

        return true;
    }

    /**
     * Validation de regex
     * @param string $value
     * @param string $key
     * @return bool
     */
    public function validateRegex(string $value, string $key): bool
    {
        $regex = new Assert\Regex(['pattern' => '/(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}/']);
        $regex->message = self::MSG_ERROR_REGEX;

        $errors = $this->validator->validate(
            $value,
            $regex
        );

        if (count($errors) > 0) {
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
     * Validation array not empty
     * @param array $data
     * @param string $key
     * @return bool
     */
    public function validateExist(array $data, string $key, string $value): bool
    {
        if (!isset($data[$value])) {
            $this->errors[] = sprintf(self::MSG_ERROR_EMPTY, $key);
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

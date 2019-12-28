<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:19
 */

namespace App\Service\Tools\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface DataValidatorInterface
{
    public function validateEmail(string $email, string $key);

    public function validateNotBlank(string $blank, string $key);

    public function validateNotNull($value, string $key);

    public function validateEqualTo(string $first, string $second, string $key);

    public function validateImage(?UploadedFile $file, string $key);

    public function validateRegex(string $value, string $key);

    public function validateExist(array $data, string $key, string $value);
}

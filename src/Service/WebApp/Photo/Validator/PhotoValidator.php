<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Photo\Validator;

use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\Validator\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class PhotoValidator
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
    public function checkCreatePhoto(array $data, ?UploadedFile $file): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateNotBlank($data['title'], 'Titre');
        $this->validatorService->validateExist($data, 'format', 'format');
        $this->validatorService->validateExist($data, 'tags', 'tags');
        $this->validatorService->validateNotNull($file, 'photo');
        $this->validatorService->validateImage($file, 'photo');

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
     * @param UploadedFile|null $file
     * @return array
     */
    public function checkUpdatePhoto(array $data, ?UploadedFile $file): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateNotBlank($data['title'], 'Titre');
        $this->validatorService->validateExist($data, 'format', 'format');
        $this->validatorService->validateExist($data, 'tags', 'tags');
        $this->validatorService->validateImage($file, 'photo');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();

        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}

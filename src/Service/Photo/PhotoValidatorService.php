<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Photo;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Tools\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class PhotoValidatorService
{
    private $validatorService;
    private $toolsService;

    public function __construct(
        DataValidatorService $dataValidatorService,
        ToolsService $toolsService
    )
    {
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
        $this->validatorService->validateCsrfToken($data['token'], 'create-photo');
        $this->validatorService->validateNotBlank($data['title'], 'Titre');
        $this->validatorService->validateExist($data, 'format', 'format');
        $this->validatorService->validateExist($data, 'tags', 'tags');
        $this->validatorService->validateNotNull($file, 'photo', 'Photo');
        $this->validatorService->validateImage($file, 'photo');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}
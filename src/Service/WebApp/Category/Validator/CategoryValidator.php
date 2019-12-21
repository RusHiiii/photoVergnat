<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Category\Validator;

use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\Tools\Validator\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class CategoryValidator
{
    const TOKEN_UPDATE = 'update-category';
    const TOKEN_CREATE = 'create-category';

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
     * Vérification d'une categorie
     * @param array $data
     * @return array
     */
    public function checkCategory(array $data, string $token): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], $token);
        $this->validatorService->validateNotBlank($data['title'], 'Titre');
        $this->validatorService->validateNotBlank($data['description'], 'Description');
        $this->validatorService->validateNotBlank($data['city'], 'Ville');
        $this->validatorService->validateNotBlank($data['lat'], 'Latitude');
        $this->validatorService->validateNotBlank($data['lng'], 'Longitude');
        $this->validatorService->validateNotBlank($data['metaDescription'], 'SEO');
        $this->validatorService->validateExist($data, 'season', 'Saison');
        $this->validatorService->validateExist($data, 'tags', 'Tags');
        $this->validatorService->validateExist($data, 'photos', 'Photos');

        /** Récupération des erreurs */
        $errors = $this->validatorService->getErrors();

        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}

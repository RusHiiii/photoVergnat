<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Category;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Tools\DataValidatorService;
use App\Service\Tools\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class CategoryValidatorService
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
     * Vérification d'une categorie
     * @param array $data
     * @return array
     */
    public function checkCreateCategory(array $data): array
    {
        /** Trim les données */
        $data = $this->toolsService->trimData($data);

        /** Validation des données */
        $this->validatorService->validateCsrfToken($data['token'], 'create-category');
        $this->validatorService->validateNotBlank($data['title'], 'Titre');
        $this->validatorService->validateNotBlank($data['description'], 'Description');
        $this->validatorService->validateNotBlank($data['city'], 'Ville');
        $this->validatorService->validateNotBlank($data['lat'], 'Latitude');
        $this->validatorService->validateNotBlank($data['lng'], 'Longitude');
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

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service;


use App\Repository\UserRepository;
use App\Service\Tools\DataValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{
    const MSG_ERROR_USER = "Opération impossible";

    private $entityManager;
    private $validatorService;
    private $userRepository;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        DataValidatorService $dataValidatorService,
        UserRepository $userRepository,
        Security $security
    )
    {
        $this->entityManager = $entityManager;
        $this->validatorService = $dataValidatorService;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * Mise à jours d'un utilisateur
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateUser(array $data): array
    {
        // Validation des données
        $validatedData = $this->checkUpdateUser($data);
        if(count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors']
            ];
        }

        // Récupération de l'utilisateur
        $currentUser = $this->security->getUser();
        $user = $this->userRepository->findByEmail($currentUser->getUsername());

        // Modification de l'utilisateur et sauvegarde
        $user->setEmail($validatedData['data']['email']);
        $user->setFirstname($validatedData['data']['firstname']);
        $user->setLastname($validatedData['data']['lastname']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Validation de la données pour la mise à jour
     * @param array $data
     * @return array
     */
    private function checkUpdateUser(array $data): array
    {
        // On trim les données
        $data = array_map('trim', $data);

        // Validation des données
        $this->validatorService->validateCsrfToken($data['token'], 'update-user');
        $this->validatorService->validateEmail($data['email'], 'mail');
        $this->validatorService->validateNotBlank($data['lastname'], 'nom');
        $this->validatorService->validateNotBlank($data['firstname'], 'prénom');

        // Traitement des erreurs
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}
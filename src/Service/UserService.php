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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{
    private $entityManager;
    private $validatorService;
    private $userRepository;
    private $security;
    private $encoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        DataValidatorService $dataValidatorService,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        Security $security
    )
    {
        $this->entityManager = $entityManager;
        $this->validatorService = $dataValidatorService;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->encoder = $passwordEncoder;
    }

    /**
     * Mise à jours d'un utilisateur
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateProfile(array $data): array
    {
        // Validation des données
        $validatedData = $this->checkUpdateProfile($data);
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
     * Mise à jour du mot de passe
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updatePassword(array $data): array
    {
        // Validation des données
        $validatedData = $this->checkUpdatePassword($data);
        if(count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors']
            ];
        }

        // Récupération de l'utilisateur
        $currentUser = $this->security->getUser();
        $user = $this->userRepository->findByEmail($currentUser->getUsername());

        // Modification de l'utilisateur et sauvegarde
        $user->setPassword($this->encoder->encodePassword($user, $validatedData['data']['password_first']));

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
    private function checkUpdateProfile(array $data): array
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

    /**
     * Validation du mot de passe
     * @param array $data
     * @return array
     */
    private function checkUpdatePassword(array $data): array
    {
        // On trim les données
        $data = array_map('trim', $data);

        // Validation des données
        $this->validatorService->validateCsrfToken($data['token'], 'update-password');
        $this->validatorService->validateEqualTo($data['password_first'],$data['password_second'], 'mot de passe');
        $this->validatorService->validateRegex($data['password_first'], 'mot de passe');

        // Traitement des erreurs
        $errors = $this->validatorService->getErrors();
        return [
            'errors' => $errors,
            'data' => $data
        ];
    }
}
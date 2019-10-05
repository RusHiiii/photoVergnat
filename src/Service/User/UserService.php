<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\User;

use App\Controller\Security\Voter\UserVoter;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    const MSG_UNKNOWN_USER = 'Utilisateur inexistant !';

    private $entityManager;
    private $userRepository;
    private $security;
    private $encoder;
    private $userValidatorService;
    private $serialize;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        Security $security,
        UserValidatorService $userValidatorService,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->encoder = $passwordEncoder;
        $this->userValidatorService = $userValidatorService;
        $this->serialize = $serializer;
    }

    /**
     * Mise à jours d'un utilisateur
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateProfile(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdateProfile($data, UserValidatorService::TOKEN_UPDATE_USER);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors']
            ];
        }

        /** Récupération de l'utilisateur */
        $user = $this->security->getUser();
        $user->setEmail($validatedData['data']['email']);
        $user->setFirstname($validatedData['data']['firstname']);
        $user->setLastname($validatedData['data']['lastname']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Mise à jours d'un utilisateur
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateUser(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdateUser($data, UserValidatorService::TOKEN_UPDATE_USER);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'user' => []
            ];
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $user = $this->userRepository->findById($validatedData['data']['id']);
        $user->setEmail($validatedData['data']['email']);
        $user->setFirstname($validatedData['data']['firstname']);
        $user->setLastname($validatedData['data']['lastname']);
        $user->setCreated(new \DateTime($validatedData['data']['created']));
        $user->setRoles($validatedData['data']['roles']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'user' => $this->serialize->serialize($user, 'json')
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
        /** Validation des données */
        $validatedData =$this->userValidatorService->checkUpdatePassword($data, UserValidatorService::TOKEN_UPDATE_PSWD);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors']
            ];
        }

        /** Récupération de l'utilisateur */
        $user = $this->security->getUser()->getUsername();
        if ($this->security->isGranted(UserVoter::EDIT, User::class) && isset($validatedData['data']['email'])) {
            $user = $this->userRepository->findByEmail($validatedData['data']['email']);
        }

        /** Modification de l'utilisateur et sauvegarde */
        $user->setPassword($this->encoder->encodePassword($user, $validatedData['data']['password_first']));

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Suppression d'un utilisateur
     * @param string $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeUser(string $data): array
    {
        /** On récupére l'utilisateur */
        $user = $this->userRepository->findById($data);
        if ($user === null) {
            return [
                'errors' => [self::MSG_UNKNOWN_USER]
            ];
        }

        /** Suppression */
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return [
            'errors' => []
        ];
    }

    /**
     * Création d'un utilisateur
     * @param array $data
     * @return array
     */
    public function createUser(array $data): array
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkCreateUser($data, UserValidatorService::TOKEN_CREATE_USER);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'user' => []
            ];
        }

        /** Insertion de l'utilisateur et sauvegarde */
        $user = new User();
        $user->setEmail($validatedData['data']['email']);
        $user->setFirstname($validatedData['data']['firstname']);
        $user->setLastname($validatedData['data']['lastname']);
        $user->setPassword($validatedData['data']['password_first']);
        $user->setRoles($validatedData['data']['roles']);

        /** Sauvegarde */
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'errors' => [],
            'user' => $this->serialize->serialize($user, 'json')
        ];
    }
}

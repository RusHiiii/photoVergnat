<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\User;

use App\Controller\Security\Voter\UserVoter;
use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\WebApp\User\Validator\UserValidator;
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
        UserValidator $userValidatorService,
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
        $validatedData = $this->userValidatorService->checkUpdateProfile($data, UserValidator::TOKEN_UPDATE_USER);
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
    public function updateUser(array $data, User $user): array
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdateUser($data, UserValidator::TOKEN_UPDATE_USER);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors'],
                'user' => []
            ];
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $user->setEmail($validatedData['data']['email']);
        $user->setFirstname($validatedData['data']['firstname']);
        $user->setLastname($validatedData['data']['lastname']);
        $user->setCreated(new \DateTime($validatedData['data']['created']));
        $user->setRoles($validatedData['data']['roles']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return [
            'errors' => [],
            'user' => $this->serialize->serialize($user, 'json', ['groups' => ['default']])
        ];
    }

    /**
     * Mise à jour du mot de passe
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updatePassword(array $data, User $user): array
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdatePassword($data, UserValidator::TOKEN_UPDATE_PSWD);
        if (count($validatedData['errors']) > 0) {
            return [
                'errors' => $validatedData['errors']
            ];
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
    public function removeUser(User $user): array
    {
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
        $validatedData = $this->userValidatorService->checkCreateUser($data, UserValidator::TOKEN_CREATE_USER);
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
            'user' => $this->serialize->serialize($user, 'json', ['groups' => ['default']])
        ];
    }

    /**
     * Récupération des actions
     * @return array
     */
    public function getLastAction(): array
    {
        $data = [];

        /** Récupération des infos */
        $users = $this->userRepository->findByLast(5);

        foreach ($users as $user) {
            $data[] = [
                'icon' => 'users',
                'action' => 'users',
                'title' => $user->getFullName(),
                'created' => $user->getCreated(),
                'updated' => $user->getUpdated()
            ];
        }

        return $data;
    }
}

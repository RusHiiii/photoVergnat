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
use App\Service\WebApp\User\Assembler\UserAssembler;
use App\Service\WebApp\User\Exceptions\UserInvalidDataException;
use App\Service\WebApp\User\Validator\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    private $entityManager;
    private $userRepository;
    private $security;
    private $encoder;
    private $userValidatorService;
    private $userAssembler;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        Security $security,
        UserValidator $userValidatorService,
        UserAssembler $userAssembler
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->encoder = $passwordEncoder;
        $this->userValidatorService = $userValidatorService;
        $this->userAssembler = $userAssembler;
    }

    /**
     * Mise à jours d'un utilisateur
     * @param array $data
     * @return User
     * @throws Exceptions\UserNotFoundException
     * @throws UserInvalidDataException
     */
    public function updateProfile(array $data, User $user): User
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdateProfile($data, UserValidator::TOKEN_UPDATE_USER);
        if (count($validatedData['errors']) > 0) {
            throw new UserInvalidDataException($validatedData['errors'], UserInvalidDataException::USER_INVALID_DATA_MESSAGE);
        }

        /** Récupération de l'utilisateur */
        $user = $this->userAssembler->editProfile($user, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Mise à jours d'un utilisateur
     * @param array $data
     * @param User $user
     * @return User
     * @throws Exceptions\UserNotFoundException
     * @throws UserInvalidDataException
     */
    public function updateUser(array $data, User $user): User
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdateUser($data, UserValidator::TOKEN_UPDATE_USER);
        if (count($validatedData['errors']) > 0) {
            throw new UserInvalidDataException($validatedData['errors'], UserInvalidDataException::USER_INVALID_DATA_MESSAGE);
        }

        /** MàJ de l'utilisateur et sauvegarde */
        $user = $this->userAssembler->editUser($user, $validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Mise à jour du mot de passe
     * @param array $data
     * @param User $user
     * @return User
     * @throws Exceptions\UserNotFoundException
     * @throws UserInvalidDataException
     */
    public function updatePassword(array $data, User $user): User
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkUpdatePassword($data, UserValidator::TOKEN_UPDATE_PSWD);
        if (count($validatedData['errors']) > 0) {
            throw new UserInvalidDataException($validatedData['errors'], UserInvalidDataException::USER_INVALID_DATA_MESSAGE);
        }

        /** Modification de l'utilisateur et sauvegarde */
        $user = $this->userAssembler->editPassword($user, $validatedData['data']['password_first']);

        /** Sauvegarde */
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Suppression d'un utilisateur
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeUser(User $user): bool
    {
        /** Suppression */
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Création d'un user
     * @param array $data
     * @return User
     * @throws UserInvalidDataException
     */
    public function createUser(array $data): User
    {
        /** Validation des données */
        $validatedData = $this->userValidatorService->checkCreateUser($data, UserValidator::TOKEN_CREATE_USER);
        if (count($validatedData['errors']) > 0) {
            throw new UserInvalidDataException($validatedData['errors'], UserInvalidDataException::USER_INVALID_DATA_MESSAGE);
        }

        /** Insertion de l'utilisateur et sauvegarde */
        $user = $this->userAssembler->create($validatedData['data']);

        /** Sauvegarde */
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
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

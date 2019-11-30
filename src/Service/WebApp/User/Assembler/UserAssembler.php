<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\User\Assembler;

use App\Entity\WebApp\User;
use App\Repository\WebApp\User\Doctrine\UserRepository;
use App\Service\WebApp\User\Exceptions\UserNotFoundException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAssembler
{
    private $userRepository;
    private $passwordEncoder;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Création d'un user
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setPassword($data['password_first']);
        $user->setRoles($data['roles']);

        return $user;
    }

    /**
     * Edition d'un user
     * @param User $user
     * @param array $data
     * @return User
     * @throws UserNotFoundException
     */
    public function edit(User $user, array $data)
    {
        if ($user == null) {
            throw new UserNotFoundException(['User inexistant'], UserNotFoundException::USER_NOT_FOUND_MESSAGE);
        }

        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);

        if (isset($data['created'])) {
            $user->setCreated(new \DateTime($data['created']));
        }

        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Edit a password
     * @param User $user
     * @param string $password
     * @return User
     * @throws UserNotFoundException
     */
    public function editPassword(User $user, string $password)
    {
        if ($user == null) {
            throw new UserNotFoundException(['User inexistant'], UserNotFoundException::USER_NOT_FOUND_MESSAGE);
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        return $user;
    }
}

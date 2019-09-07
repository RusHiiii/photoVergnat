<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 07/09/2019
 * Time: 15:26
 */

namespace App\Controller\Security\Voter;


use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    const REMOVE = 'remove';
    const EDIT = 'edit';
    const CREATE = 'create';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::REMOVE, self::EDIT, self::CREATE])) {
            return false;
        }

        if ($subject != User::class) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::EDIT:
                return $this->canCreateOrEdit($user);
            case self::REMOVE:
                return $this->canRemove($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreateOrEdit(User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function canRemove(User $user)
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return false;
    }
}
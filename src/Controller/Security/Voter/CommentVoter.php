<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 07/09/2019
 * Time: 15:26
 */

namespace App\Controller\Security\Voter;

use App\Entity\WebApp\Comment;
use App\Entity\WebApp\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CommentVoter extends Voter
{
    const REMOVE = 'remove';
    const EDIT = 'edit';
    const VIEW = 'view';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::REMOVE, self::EDIT, self::VIEW])) {
            return false;
        }

        if (!$subject instanceof Comment) {
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
            case self::EDIT:
            case self::VIEW:
                return $this->canEdit($user);
            case self::REMOVE:
                return $this->canRemove($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * Vérifier si le user peux créer ou supprimer
     * @param User $user
     * @return bool
     */
    private function canRemove(User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    /**
     * Vérifier si le user peux créer ou editer
     * @param User $user
     * @return bool
     */
    private function canEdit(User $user)
    {
        if ($this->security->isGranted('ROLE_AUTHOR')) {
            return true;
        }

        return false;
    }
}

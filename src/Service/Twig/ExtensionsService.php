<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 08/09/2019
 * Time: 21:44
 */
namespace App\Service\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExtensionsService extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('hasRole', [$this, 'hasRole']),
        ];
    }

    /**
     * Vérifie le role de l'utilsiateur
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function hasRole(User $user, string $role)
    {
        if (in_array($role, $user->getRoles())) {
            return true;
        }
        return false;
    }
}

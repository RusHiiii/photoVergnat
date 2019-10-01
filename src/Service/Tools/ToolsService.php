<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\Tools;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class ToolsService
{
    /**
     * Trim les données
     * @param $data
     * @return array|string|null
     */
    public function trimData($data)
    {
        if ($data === null) {
            return null;
        }

        if (is_array($data)) {
            return array_map(array($this, 'trimData'), $data);
        } else {
            return trim($data);
        }
    }
}

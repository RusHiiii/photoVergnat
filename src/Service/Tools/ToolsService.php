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
     * @param mixed $data
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

    /**
     * Slugify une valeur
     * @param string $value
     * @return string
     */
    public function slugify(string $value)
    {
        $string = str_replace(' ', '-', $value);

        return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string));
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @return bool|int
     */
    public function compareByUpdated($a, $b)
    {
        if ($a['updated'] == $b['updated']) {
            return 0;
        }

        return $a['updated'] < $b['updated'];
    }
}

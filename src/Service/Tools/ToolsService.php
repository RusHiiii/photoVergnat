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
        $unwanted_array = [
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A',
            'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
            'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U',
            'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a',
            'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
            'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u',
            'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
        ];
        $slug = strtr($value, $unwanted_array);
        $slug = str_replace(' ', '-', $slug);

        return strtolower($slug);
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

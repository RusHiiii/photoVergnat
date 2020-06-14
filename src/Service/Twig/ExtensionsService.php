<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 08/09/2019
 * Time: 21:44
 */
namespace App\Service\Twig;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Photo;
use App\Entity\WebApp\Tag;
use App\Entity\WebApp\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExtensionsService extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('hasRole', [$this, 'hasRole']),
            new TwigFilter('getMainPhotoUrl', [$this, 'getMainPhotoUrl']),
            new TwigFilter('shuffle', [$this, 'shuffle']),
            new TwigFilter('getClassName', [$this, 'getClassName']),
            new TwigFilter('getAllClassName', [$this, 'getAllClassName']),
            new TwigFilter('slugify', [$this, 'slugify']),
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

    /**
     * Récupération image à la une
     * @param Category $category
     * @return string|null
     */
    public function getMainPhotoUrl(Category $category, string $format)
    {
        foreach ($category->getPhotos() as $value) {
            if ($value->getType()->getTitle() == $format) {
                return $value->getFile();
            }
        }

        return null;
    }

    /**
     * Shuffle un array
     * @param array $array
     * @return array
     */
    public function shuffle(array $array)
    {
        shuffle($array);
        return $array;
    }

    /**
     * Génère les classes
     * @param mixed $photos
     * @return array
     */
    public function getAllClassName($photos)
    {
        $className = [];
        foreach ($photos as $photo) {
            $className[] = $this->getClassName($photo);
        }

        return array_unique(array_reduce($className, 'array_merge', []));
    }

    /**
     * Génère une classe
     * @param Photo $photo
     * @return array
     */
    public function getClassName(Photo $photo)
    {
        $className = [];
        foreach ($photo->getTags() as $tag) {
            $className[] = str_replace(' ', '-', $tag->getTitle());
        }

        return $className;
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
            'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '\'' => ''
        ];
        $slug = strtr($value, $unwanted_array);
        $slug = str_replace(' ', '-', $slug);

        return strtolower($slug);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:12
 */

namespace App\Repository\WebApp\Photo;

use App\Entity\WebApp\Category;

interface PhotoRepositoryInterface
{
    public function findById($value);

    public function findByUnused();

    public function findByUnusedAndCategory(Category $category);

    public function findByUsed(string $type);

    public function findByLast(int $nb);

    public function countByMonth(\DateTime $date);
}

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:10
 */

namespace App\Repository\WebApp\Category;

interface CategoryRepositoryInterface
{
    public function findById($value);

    public function findByActive(string $value);

    public function findByLast(int $nb);
}

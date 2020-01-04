<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:14
 */

namespace App\Repository\WebApp\Tag;

interface TagRepositoryInterface
{
    public function findById(int $id);

    public function findByType(string $type);

    public function findByLast(int $nb);
}

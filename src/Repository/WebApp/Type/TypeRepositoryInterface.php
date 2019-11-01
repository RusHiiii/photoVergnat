<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:16
 */

namespace App\Repository\WebApp\Type;


interface TypeRepositoryInterface
{
    public function findById(int $id);
}
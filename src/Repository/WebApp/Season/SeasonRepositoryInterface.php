<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:14
 */

namespace App\Repository\WebApp\Season;

interface SeasonRepositoryInterface
{
    public function findById(int $id);
}

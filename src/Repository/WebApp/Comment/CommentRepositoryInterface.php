<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:11
 */

namespace App\Repository\WebApp\Comment;

interface CommentRepositoryInterface
{
    public function findByLast(int $nb = 5);
}

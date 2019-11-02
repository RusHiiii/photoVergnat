<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 17:17
 */

namespace App\Repository\WebApp\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email);

    public function findById(int $id);

    public function findByRole(string $role);

    public function findByLast(int $nb);
}

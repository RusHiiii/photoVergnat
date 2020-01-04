<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 18:09
 */

namespace App\Service\WebApp\User\Exceptions;

use Throwable;

class UserNotFoundException extends \Exception
{
    const USER_NOT_FOUND_MESSAGE = 'User not found';

    private $context;

    public function __construct(array $context = [], string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 18:09
 */

namespace App\Service\WebApp\Type\Exceptions;

use Throwable;

class TypeNotFoundException extends \Exception
{
    const TYPE_NOT_FOUND_MESSAGE = 'Type not found';

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

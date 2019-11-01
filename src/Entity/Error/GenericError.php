<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 18:46
 */

namespace App\Entity\Error;

class GenericError
{
    private $context;
    private $type;
    private $message;
    private $code;

    public function __construct($context = [], $type = null, $message = null, $code = null)
    {
        $this->context = $context;
        $this->type = $type;
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param null $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
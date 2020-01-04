<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 28/12/2019
 * Time: 21:25
 */

namespace App\Entity\Core;


use Symfony\Component\HttpFoundation\Response;

class SerializedResponse extends Response
{
    public function __construct($content = '', $statusCode = 400)
    {
        parent::__construct($content, $statusCode);
        $this->headers->set('Content-Type', 'application/json');
    }
}
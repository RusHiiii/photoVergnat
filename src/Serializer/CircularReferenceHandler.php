<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 29/09/2019
 * Time: 15:52
 */

namespace App\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        return $object->getId();
    }
}

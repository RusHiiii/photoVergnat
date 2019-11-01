<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 01/11/2019
 * Time: 18:47
 */

namespace App\Service\Tools\Error\Factory;

use App\Entity\Error\GenericError;

class ErrorFactory
{
    /**
     * Création de l'erreur
     * @param \Exception $e
     * @return GenericError
     * @throws \ReflectionException
     */
    public function create(\Exception $e)
    {
        $genericError = new GenericError();

        $genericError->setType((new \ReflectionClass($e))->getShortName());
        $genericError->setMessage($e->getMessage());
        $genericError->setCode($e->getCode());
        $genericError->setContext($e->getContext());

        return $genericError;
    }
}
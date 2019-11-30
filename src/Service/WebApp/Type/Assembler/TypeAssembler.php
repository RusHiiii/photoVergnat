<?php
/**
 * Created by PhpStorm.
 * User: Flo
 * Date: 31/08/2019
 * Time: 15:26
 */

namespace App\Service\WebApp\Type\Assembler;

use App\Entity\WebApp\Type;
use App\Repository\WebApp\Type\Doctrine\TypeRepository;
use App\Service\WebApp\Type\Exceptions\TypeNotFoundException;
use App\Service\WebApp\Type\Exceptions\UserNotFoundException;

class TypeAssembler
{
    private $typeRepository;

    public function __construct(
        TypeRepository $typeRepository
    ) {
        $this->typeRepository = $typeRepository;
    }

    /**
     * Création d'un type
     * @param array $data
     * @return Type
     */
    public function create(array $data)
    {
        $type = new Type();
        $type->setTitle($data['title']);

        return $type;
    }

    /**
     * Edition d'un type
     * @param Type $type
     * @param array $data
     * @return Type
     * @throws TypeNotFoundException
     */
    public function edit(Type $type, array $data)
    {
        if ($type == null) {
            throw new TypeNotFoundException(['Type inexistant'], TypeNotFoundException::TYPE_NOT_FOUND_MESSAGE);
        }

        $type->setTitle($data['title']);

        return $type;
    }
}

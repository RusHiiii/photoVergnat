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
use App\Service\WebApp\Type\Exceptions\NotFoundException;

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
     * @throws NotFoundException
     */
    public function edit(Type $type, array $data)
    {
        if ($type === null) {
            throw new NotFoundException([], 'Type not found');
        }

        $type->setTitle($data['title']);

        return $type;
    }
}

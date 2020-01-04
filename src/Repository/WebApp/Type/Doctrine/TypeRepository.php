<?php

namespace App\Repository\WebApp\Type\Doctrine;

use App\Entity\WebApp\Type;
use App\Repository\WebApp\Type\TypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TypeRepository extends ServiceEntityRepository implements TypeRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Type::class);
    }

    /**
     * Récupération par ID
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $id)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Récupération par le nom
     * @param string $name
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByName(string $name)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.title = :title')
            ->setParameter('title', $name)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}

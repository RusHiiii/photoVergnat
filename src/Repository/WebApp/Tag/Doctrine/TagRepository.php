<?php

namespace App\Repository\WebApp\Tag\Doctrine;

use App\Entity\WebApp\Tag;
use App\Repository\WebApp\Tag\TagRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TagRepository extends ServiceEntityRepository implements TagRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
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
     * Récupération par type
     * @param string $type
     * @return mixed
     */
    public function findByType(string $type)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupération des derniers tags
     * @return mixed
     */
    public function findByLast(int $nb)
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository\WebApp\Season\Doctrine;

use App\Entity\WebApp\Season;
use App\Repository\WebApp\Season\SeasonRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SeasonRepository extends ServiceEntityRepository implements SeasonRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Season::class);
    }

    /**
     * Récupération par ID
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * Récupération par le titre
     * @param string $name
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByName(string $name)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.title = :title')
            ->setParameter('title', $name)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}

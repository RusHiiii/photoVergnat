<?php

namespace App\Repository\WebApp\Category\Doctrine;

use App\Entity\WebApp\Category;
use App\Repository\WebApp\Category\CategoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Récupération de la catégorie
     * @param $value
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Récupération par active
     * @param string $value
     * @return array
     */
    public function findByActive(string $value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.active = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupération des dernieres catégorie
     * @param int $nb
     * @return array
     */
    public function findByLast(int $nb): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult()
            ;
    }
}

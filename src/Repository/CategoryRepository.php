<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
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
     * @return Category|null
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

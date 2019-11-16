<?php

namespace App\Repository\WebApp\Photo\Doctrine;

use App\Entity\WebApp\Category;
use App\Entity\WebApp\Photo;
use App\Repository\WebApp\Photo\PhotoRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PhotoRepository extends ServiceEntityRepository implements PhotoRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Récupération par ID
     * @param $value
     * @return Photo|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($value): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Récupération des photos
     * @return array
     */
    public function findByUnused(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category IS NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupération des photos
     * @return array
     */
    public function findByUnusedAndCategory(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category IS NULL')
            ->orWhere('p.category = :cat')
            ->setParameters(['cat' => $category])
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupération des thumbnails
     * @return array
     */
    public function findByUsed(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category IS NOT NULL')
            ->innerJoin('p.type', 'type', 'type.id = p.type')
            ->andWhere('type.title = :val')
            ->setParameter('val', $type)
            ->orderBy('p.id')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupération des dernieres photos
     * @return mixed
     */
    public function findByLast(int $nb)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'occurence
     * @param $date
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByMonth(\DateTime $date)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.created BETWEEN :begin AND :end')
            ->setParameter('begin', $date->format('Y-m-d'))
            ->setParameter('end', $date->format('Y-m-t'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}

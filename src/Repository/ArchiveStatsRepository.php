<?php

namespace App\Repository;

use App\Entity\ArchiveStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ArchiveStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArchiveStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArchiveStats[]    findAll()
 * @method ArchiveStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveStatsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ArchiveStats::class);
    }

//    /**
//     * @return ArchiveStats[] Returns an array of ArchiveStats objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArchiveStats
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

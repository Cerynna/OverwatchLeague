<?php

namespace App\Repository;

use App\Entity\GamePlayed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GamePlayed|null find($id, $lockMode = null, $lockVersion = null)
 * @method GamePlayed|null findOneBy(array $criteria, array $orderBy = null)
 * @method GamePlayed[]    findAll()
 * @method GamePlayed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamePlayedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GamePlayed::class);
    }

//    /**
//     * @return GamePlayed[] Returns an array of GamePlayed objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GamePlayed
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

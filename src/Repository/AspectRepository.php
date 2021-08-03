<?php

namespace App\Repository;

use App\Entity\Aspect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Aspect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aspect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aspect[]    findAll()
 * @method Aspect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AspectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aspect::class);
    }

    // /**
    //  * @return Aspect[] Returns an array of Aspect objects
    //  */
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
    public function findOneBySomeField($value): ?Aspect
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

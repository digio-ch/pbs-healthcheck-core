<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\aggregatedPersonRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method aggregatedPersonRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method aggregatedPersonRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method aggregatedPersonRole[]    findAll()
 * @method aggregatedPersonRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class aggregatedPersonRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, aggregatedPersonRole::class);
    }

    // /**
    //  * @return aggregatedPersonRole[] Returns an array of aggregatedPersonRole objects
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
    public function findOneBySomeField($value): ?aggregatedPersonRole
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

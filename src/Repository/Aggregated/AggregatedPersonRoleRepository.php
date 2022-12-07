<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedPersonRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AggregatedPersonRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AggregatedPersonRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AggregatedPersonRole[]    findAll()
 * @method AggregatedPersonRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AggregatedPersonRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedPersonRole::class);
    }

    /**
     * @return AggregatedPersonRole[]
     */
    public function getUnfinished(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.end_at IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getHighestAggregatedMidataIndex(): int
    {
        return $this->createQueryBuilder('a')
            ->select('MAX(a.midata)')
            ->getQuery()
            ->getResult()[0][1];
    }

    // /**
    //  * @return AggregatedPersonRole[] Returns an array of AggregatedPersonRole objects
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
    public function findOneBySomeField($value): ?AggregatedPersonRole
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

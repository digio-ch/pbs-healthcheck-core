<?php


namespace App\Repository;


use App\Entity\WidgetQuap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WidgetQuap|null find($id, $lockMode = null, $lockVersion = null)
 * @method WidgetQuap|null findOneBy(array $criteria, array $orderBy = null)
 * @method WidgetQuap[]    findAll()
 * @method WidgetQuap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetQuapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetQuap::class);
    }

    // /**
    //  * @return WidgetQuap[] Returns an array of WidgetQuap objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WidgetQuap
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

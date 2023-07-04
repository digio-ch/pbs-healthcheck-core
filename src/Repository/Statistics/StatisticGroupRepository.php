<?php

namespace App\Repository\Statistics;

use App\Entity\Statistics\StatisticGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatisticGroup>
 *
 * @method StatisticGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticGroup[]    findAll()
 * @method StatisticGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatisticGroup::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(StatisticGroup $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(StatisticGroup $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function deleteAll()
    {
        $this->_em->createQueryBuilder()
            ->delete(StatisticGroup::class, 'g')
            ->getQuery()
            ->execute();
        $this->_em->flush();
        $metadata = $this->_em->getClassMetaData(StatisticGroup::class);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    // /**
    //  * @return StatisticGroup[] Returns an array of StatisticGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StatisticGroup
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\LevelUpLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LevelUpLog>
 *
 * @method LevelUpLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method LevelUpLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method LevelUpLog[]    findAll()
 * @method LevelUpLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelUpLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LevelUpLog::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(LevelUpLog $entity, bool $flush = true): void
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
    public function remove(LevelUpLog $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function retrieveLastMonth()
    {
        $cutoffDate = (new \DateTimeImmutable('first day of last month'))->setTime(0,0);
        return $this->createQueryBuilder('l')
            ->where('l.date >= :date')
            ->orderBy('l.person')
            ->addOrderBy('l.level')
            ->setParameter('date', $cutoffDate)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return LevelUpLog[] Returns an array of LevelUpLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LevelUpLog
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

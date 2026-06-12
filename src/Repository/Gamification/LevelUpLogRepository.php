<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\LevelUpLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function add(LevelUpLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LevelUpLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function retrieveLastMonth()
    {
        $cutoffDate = (new \DateTimeImmutable('first day of last month'))->setTime(0, 0);
        return $this->createQueryBuilder('l')
            ->where('l.date >= :date')
            ->orderBy('l.person')
            ->addOrderBy('l.level')
            ->setParameter('date', $cutoffDate)
            ->getQuery()
            ->getResult();
    }
}

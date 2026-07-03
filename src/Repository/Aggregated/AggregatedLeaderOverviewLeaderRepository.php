<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedLeaderOverviewLeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AggregatedLeaderOverviewLeader>
 */
class AggregatedLeaderOverviewLeaderRepository extends ServiceEntityRepository
{
    /**
     * AggregatedLeaderOverviewLeaderRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedLeaderOverviewLeader::class);
    }

    public function findAllByGroupTypeAndDate(int $mainGroupId, string $groupType, string $date): mixed
    {
        return $this->createQueryBuilder('lol')
            ->innerJoin('lol.leaderOverview', 'lo')
            ->where('lo.dataPointDate = :date')
            ->andWhere('lo.group = :groupId')
            ->andWhere('lo.groupType = :groupType')
            ->orderBy('lol.birthday', 'ASC')
            ->setParameter('date', $date)
            ->setParameter('groupId', $mainGroupId)
            ->setParameter('groupType', $groupType)
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\WidgetDemographicCamp;
use Doctrine\Persistence\ManagerRegistry;

class WidgetDemographicCampRepository extends AggregatedEntityRepository
{
    /**
     * WidgetDemographicCampRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetDemographicCamp::class);
    }

    /**
     * @param string $from
     * @param string $to
     * @param Group $mainGroup
     * @return int|mixed|string
     */
    public function getAllForPeriodAndMainGroup(string $from, string $to, Group $mainGroup)
    {
        return $this->createQueryBuilder('dc')
            ->innerJoin('dc.demographicCampGroups', 'cg')
            ->where('dc.dataPointDate >= :from')
            ->andWhere('dc.dataPointDate <= :to')
            ->andWhere('cg.group = :mainGroup')
            ->setParameter('mainGroup', $mainGroup)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }
}

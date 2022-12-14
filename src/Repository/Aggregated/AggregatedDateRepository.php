<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedDate;
use Doctrine\Persistence\ManagerRegistry;

class AggregatedDateRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedDate::class);
    }

    public function findDataPointDatesByGroupIds($groups)
    {
        return $this->createQueryBuilder('widget')
            ->select('widget.dataPointDate')
            ->distinct(true)
            ->where('widget.group IN (:groups)')
            ->addOrderBy('widget.dataPointDate', 'DESC')
            ->setParameter('groups', $groups)
            ->getQuery()
            ->getArrayResult();
    }
}

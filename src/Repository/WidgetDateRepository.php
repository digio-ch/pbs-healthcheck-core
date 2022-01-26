<?php

namespace App\Repository;

use App\Entity\WidgetDate;
use Doctrine\Persistence\ManagerRegistry;

class WidgetDateRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetDate::class);
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

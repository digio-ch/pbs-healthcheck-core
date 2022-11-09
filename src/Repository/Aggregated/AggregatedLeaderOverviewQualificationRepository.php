<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedLeaderOverviewQualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AggregatedLeaderOverviewQualificationRepository extends ServiceEntityRepository
{
    /**
     * AggregatedLeaderOverviewQualificationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedLeaderOverviewQualification::class);
    }
}

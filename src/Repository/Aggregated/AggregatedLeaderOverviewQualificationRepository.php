<?php

declare(strict_types=1);

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedLeaderOverviewQualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AggregatedLeaderOverviewQualification>
 */
class AggregatedLeaderOverviewQualificationRepository extends ServiceEntityRepository
{
    /**
     * AggregatedLeaderOverviewQualificationRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedLeaderOverviewQualification::class);
    }
}

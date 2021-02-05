<?php

namespace App\Repository;

use App\Entity\LeaderOverviewQualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LeaderOverviewQualificationRepository extends ServiceEntityRepository
{
    /**
     * LeaderOverviewQualificationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaderOverviewQualification::class);
    }
}

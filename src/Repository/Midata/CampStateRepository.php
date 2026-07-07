<?php

declare(strict_types=1);

namespace App\Repository\Midata;

use App\Entity\Midata\CampState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampState>
 */
class CampStateRepository extends ServiceEntityRepository
{
    /**
     * CampStateRepository constructor.
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, CampState::class);
    }
}

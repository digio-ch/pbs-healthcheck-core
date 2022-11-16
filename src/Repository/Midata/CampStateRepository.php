<?php

namespace App\Repository\Midata;

use App\Entity\Midata\CampState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CampStateRepository extends ServiceEntityRepository
{
    /**
     * CampStateRepository constructor.
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, CampState::class);
    }
}

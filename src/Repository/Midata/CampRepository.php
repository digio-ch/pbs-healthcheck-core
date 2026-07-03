<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Camp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Camp>
 */
class CampRepository extends ServiceEntityRepository
{
    /**
     * PersonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camp::class);
    }
}

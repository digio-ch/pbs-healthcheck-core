<?php

namespace App\Repository;

use App\Entity\Camp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

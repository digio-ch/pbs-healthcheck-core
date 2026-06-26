<?php

namespace App\Repository\Midata;

use App\Entity\Midata\EventDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventDateRepository extends ServiceEntityRepository
{
    /**
     * EventDateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventDate::class);
    }
}

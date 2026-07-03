<?php

declare(strict_types=1);

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
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camp::class);
    }
}

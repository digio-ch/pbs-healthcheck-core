<?php

declare(strict_types=1);

namespace App\Repository\Midata;

use App\Entity\Midata\PersonQualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonQualification>
 */
class PersonQualificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonQualification::class);
    }
}

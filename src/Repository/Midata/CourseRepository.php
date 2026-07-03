<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    /**
     * CourseRepository constructor.
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Course::class);
    }
}

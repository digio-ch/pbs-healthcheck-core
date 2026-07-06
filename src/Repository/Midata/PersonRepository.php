<?php

declare(strict_types=1);

namespace App\Repository\Midata;

use App\Entity\Midata\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 */
class PersonRepository extends ServiceEntityRepository
{
    /**
     * PersonRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function save(Person $person): void
    {
        $this->getEntityManager()->persist($person);
        $this->getEntityManager()->flush();
    }
}

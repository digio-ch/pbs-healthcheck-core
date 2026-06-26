<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonRepository extends ServiceEntityRepository
{
    /**
     * PersonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * @param Person $person
     */
    public function save(Person $person)
    {
        $this->getEntityManager()->persist($person);
        $this->getEntityManager()->flush();
    }
}

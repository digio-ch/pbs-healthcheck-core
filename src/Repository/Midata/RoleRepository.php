<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function getOneByRoleType(string $roleType)
    {
        return $this->createQueryBuilder('r')
            ->where('r.roleType =:roleType')
            ->setParameter('roleType', $roleType)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\PermissionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PermissionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PermissionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PermissionType[]    findAll()
 * @method PermissionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PermissionType::class);
    }
}

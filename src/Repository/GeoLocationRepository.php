<?php

namespace App\Repository;

use App\Entity\GeoLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeoLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeoLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeoLocation[]    findAll()
 * @method GeoLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeoLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeoLocation::class);
    }
}

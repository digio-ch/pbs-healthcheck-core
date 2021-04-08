<?php


namespace App\Repository;


use App\Entity\WidgetGeoLocation;
use Doctrine\Persistence\ManagerRegistry;

class WidgetGeoLocationRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetGeoLocation::class);
    }
}

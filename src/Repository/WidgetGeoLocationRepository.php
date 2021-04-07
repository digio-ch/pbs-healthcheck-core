<?php


namespace App\Repository;


use App\Entity\WidgetGeoLocation;
use Doctrine\Persistence\ManagerRegistry;

class WidgetGeoLocationRepository extends AggregatedEntityRepository
{
    /**
     * WidgetGeoLocationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetGeoLocation::class);
    }

    public function findOneByAddress(int $zip, string $town, string $address): WidgetGeoLocation
    {
        return $this->createQueryBuilder('geo')
            ->where('geo.zip == :zip')
            ->andWhere('geo.town == :town')
            ->andWhere('geo.address == :address')
            ->setParameter('zip', $zip)
            ->setParameter('town', $town)
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult()
            ->execute();
    }

    /**
     * @param WidgetGeoLocation $geoLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(WidgetGeoLocation $geoLocation)
    {
        $this->getEntityManager()->persist($geoLocation);
        $this->getEntityManager()->flush();
    }
}

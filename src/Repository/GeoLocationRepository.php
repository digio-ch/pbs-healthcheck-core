<?php


namespace App\Repository;


use App\Entity\GeoLocation;
use Doctrine\Persistence\ManagerRegistry;

class GeoLocationRepository extends AggregatedEntityRepository
{
    /**
     * GeoLocationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeoLocation::class);
    }

    /**
     * @param int $zip
     * @param string $town
     * @param string $address
     * @return GeoLocation
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByAddress(int $zip, string $town, string $address): GeoLocation
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
     * @param GeoLocation $geoLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(GeoLocation $geoLocation)
    {
        $this->getEntityManager()->persist($geoLocation);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function wipe(): void
    {
        foreach ($this->findAll() as $entity) {
            $this->remove($entity);
        }
        $this->getEntityManager()->flush();
    }
}

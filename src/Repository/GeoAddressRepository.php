<?php


namespace App\Repository;


use App\Entity\GeoAddress;
use Doctrine\Persistence\ManagerRegistry;

class GeoAddressRepository extends AggregatedEntityRepository
{
    /**
     * GeoAddressRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeoAddress::class);
    }

    /**
     * @param int $zip
     * @param string $town
     * @param string $street
     * @param string $houseNumber
     * @return GeoAddress|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByAddress(int $zip, string $town, string $street, string $houseNumber): ?GeoAddress
    {
        return $this->createQueryBuilder('geo')
            ->where('geo.zip = :zip')
            ->andWhere('geo.town = :town')
            ->andWhere('geo.address = :street')
            ->andWhere('geo.house = :house')
            ->setParameter('zip', $zip)
            ->setParameter('town', $town)
            ->setParameter('street', $street)
            ->setParameter('house', $houseNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param GeoAddress $geoLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(GeoAddress $geoLocation)
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

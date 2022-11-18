<?php

namespace App\Repository\Admin;

use App\Entity\Admin\GeoAddress;
use App\Repository\Aggregated\AggregatedEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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
     * @param string $house
     * @return GeoAddress|null
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findIdByAddress(int $zip, string $town, string $street, string $house): ?GeoAddress
    {
        $houseNumber = $house;
        if (preg_match('/(\d+[a-z])/', $houseNumber)) {
            $houseNumber = substr($houseNumber, 0, strlen($houseNumber) - 1);
        }

        $query = '
            SELECT geo.* FROM admin_geo_address AS geo
            WHERE (geo.zip = ? OR geo.town = ?)
                AND geo.address = ?
                AND (geo.house = ? OR geo.house = ?)
            LIMIT 1
        ';

        $mapping = new ResultSetMappingBuilder($this->getEntityManager());
        $mapping->addRootEntityFromClassMetadata(GeoAddress::class, 'geo');
        return $this->getEntityManager()->createNativeQuery($query, $mapping)
            ->setParameter(1, $zip)
            ->setParameter(2, strtolower($town))
            ->setParameter(3, strtolower($street))
            ->setParameter(4, strtolower($house))
            ->setParameter(5, strtolower($houseNumber))
            ->getOneOrNullResult();
    }

    /**
     * @throws \Doctrine\Persistence\Mapping\MappingException|\Doctrine\DBAL\Exception
     */
    public function wipe(): void
    {
        $connection = $this->getEntityManager()->getConnection();
        $connection->executeQuery("DELETE FROM admin_geo_address");
    }
}

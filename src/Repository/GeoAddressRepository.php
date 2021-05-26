<?php

namespace App\Repository;

use App\Entity\GeoAddress;
use Doctrine\DBAL\ParameterType;
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
    public function findIdByAddress(int $zip, string $town, string $street, string $house): ?int
    {
        $houseNumber = $house;
        if (preg_match('/(\d+[a-z])/', $houseNumber)) {
            $houseNumber = substr($houseNumber, 0, strlen($houseNumber) - 1);
        }

        $connection = $this->_em->getConnection();
        $statement = $connection->executeQuery(
            "SELECT * FROM admin_geo_address AS geo
            WHERE LOWER(geo.address) = LOWER(?)
            AND (LOWER(geo.house) = LOWER(?) OR LOWER(geo.house) = LOWER(?))
            AND (geo.zip = ? OR LOWER(geo.town) = LOWER(?))",
            [$street, $house, $houseNumber, $zip, $town],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, ParameterType::STRING]
        );
        $id = $statement->fetchOne();

        return $id ? $id : null;
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

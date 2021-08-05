<?php

namespace App\Repository;

use App\Entity\WidgetGeoLocation;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class WidgetGeoLocationRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetGeoLocation::class);
    }

    /**
     * @param string $date
     * @param string $groupType
     * @param int $groupId
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAllForDateAndGroupType(string $date, string $groupType, int $groupId, array $peopleTypes): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->executeQuery(
            "SELECT * FROM hc_widget_geo_location AS location
                WHERE location.data_point_date = ?
                AND location.group_type = ?
                AND location.group_id = ?
                AND location.person_type IN (?);",
            [
                $date,
                $groupType,
                $groupId,
                $peopleTypes
            ],
            [
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::INTEGER,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll();
    }
}

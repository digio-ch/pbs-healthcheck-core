<?php

namespace App\Repository;

use App\Entity\WidgetDemographicEnteredLeft;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class WidgetDemographicEnteredLeftRepository extends AggregatedEntityRepository
{
    /**
     * WidgetDemographicEnteredLeftRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetDemographicEnteredLeft::class);
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $mainGroupId
     * @param string $groupType
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findNewExitMembersCount(string $from, string $to, int $mainGroupId, string $groupType)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, (SUM(new_count_m) + SUM(new_count_f)) as new_sum, (SUM(exit_count_m) + SUM(exit_count_f)) as exit_sum
                  FROM hc_widget_demographic_entered_left
                  WHERE data_point_date BETWEEN ? AND ? AND 
                        hc_widget_demographic_entered_left.group_id = ? AND
                        hc_widget_demographic_entered_left.group_type = ?
                  GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $mainGroupId, $groupType],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::STRING]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findNewExitLeadersCount(string $from, string $to, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, (SUM(new_count_leader_m) + SUM(new_count_leader_f)) as new_sum, (SUM(exit_count_leader_m) + SUM(exit_count_leader_f)) as exit_sum
                  FROM hc_widget_demographic_entered_left
                  WHERE data_point_date BETWEEN ? AND ? AND 
                        hc_widget_demographic_entered_left.group_id = ? AND
                        hc_widget_demographic_entered_left.group_type IN (?)
                  GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }
}

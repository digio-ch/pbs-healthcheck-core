<?php

namespace App\Repository;

use App\Entity\aggregated\AggregatedDemographicGroup;
use App\Service\DataProvider\WidgetDataProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class WidgetDemographicGroupRepository extends AggregatedEntityRepository
{
    /**
     * WidgetDemographicGroupRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedDemographicGroup::class);
    }

    // members-group date queries

    /**
     * @param string $date
     * @param string $groupType
     * @param int $parentGroupId
     * @return bool|false|mixed
     * @throws DBALException
     */
    public function findMembersCountForDateAndGroupType(string $date, string $groupType, int $parentGroupId)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count + f_count + u_count) 
                    FROM hc_widget_demographic_group 
                    WHERE data_point_date = ? AND group_type = ? AND group_id = ?",
            [$date, $groupType, $parentGroupId],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
        );
        return $statement->fetchColumn();
    }

    /**
     * @param string $date
     * @param int $parentGroupId
     * @param array $groupTypes
     * @return bool|false|mixed
     * @throws DBALException
     */
    public function findTotalLeadersCountForDate(string $date, int $parentGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count_leader + f_count_leader + u_count_leader) 
                    FROM hc_widget_demographic_group 
                    WHERE data_point_date = ? AND 
                    group_id = ? AND
                    group_type IN (?)",
            [$date, $parentGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchColumn();
    }

    /**
     * @param string $date
     * @param string $groupType
     * @param int $parentGroupId
     * @return bool|false|mixed
     * @throws DBALException
     */
    public function findLeadersCountForDateAndGroupType(string $date, string $groupType, int $parentGroupId)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count_leader + f_count_leader)
                    FROM hc_widget_demographic_group 
                    WHERE data_point_date = ? AND group_type = ? AND group_id = ?",
            [$date, $groupType, $parentGroupId],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER]
        );
        return $statement->fetchColumn();
    }

    // members-group date period queries

    /**
     * @param string $from
     * @param string $to
     * @param int $parentGroupId
     * @param string $groupType
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findMembersCountForDateRangeAndGroupType(
        string $from,
        string $to,
        int $parentGroupId,
        string $groupType
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, SUM(m_count + f_count) as total
                    FROM hc_widget_demographic_group
                    WHERE data_point_date BETWEEN ? AND ? AND group_id = ? AND group_type = ?
                    GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $parentGroupId, $groupType],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, ParameterType::STRING]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $parentGroupId
     * @param array $groupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findLeadersCountForDateRangeAndGroupTypes(
        string $from,
        string $to,
        int $parentGroupId,
        array $groupTypes
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, SUM(m_count_leader + f_count_leader) as total
                    FROM hc_widget_demographic_group
                    WHERE data_point_date BETWEEN ? AND ? AND group_id = ? AND group_type IN (?)
                    GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $parentGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    // members-gender date queries

    /**
     * @param string $date
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function getAllGenderMemberCountForDate(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count) as m, SUM(f_count) as f, SUM(u_count) as u 
                  FROM hc_widget_demographic_group 
                  WHERE hc_widget_demographic_group.data_point_date = ?
                    AND hc_widget_demographic_group.group_id = ?
                    AND hc_widget_demographic_group.group_type IN (?)",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $date
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function getLeaderCountForDate(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count_leader) as m, SUM(f_count_leader) as f, SUM(u_count_leader) as u
                  FROM hc_widget_demographic_group 
                  WHERE hc_widget_demographic_group.data_point_date = ? 
                    AND hc_widget_demographic_group.group_id = ?
                    AND hc_widget_demographic_group.group_type IN (?);",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $date
     * @param int $mainGroupId
     * @param array $subGroupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function getAllGenderTotalCountForDate(string $date, int $mainGroupId, array $subGroupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT (SUM(m_count) + SUM(m_count_leader)) as m, (SUM(f_count) + SUM(f_count_leader)) as f, (SUM(u_count) + SUM(u_count_leader)) as u 
                  FROM hc_widget_demographic_group 
                  WHERE hc_widget_demographic_group.data_point_date = ? 
                    AND hc_widget_demographic_group.group_id = ?
                    AND hc_widget_demographic_group.group_type IN (?);",
            [$date, $mainGroupId, $subGroupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    // members-gender date period queries

    /**
     * @param string $from
     * @param string $to
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findMemberCountForDatePeriodByGroupTypes(
        string $from,
        string $to,
        int $mainGroupId,
        array $groupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, SUM(m_count) as m, SUM(f_count) as f, SUM(u_count) as u 
                  FROM hc_widget_demographic_group 
                  WHERE (hc_widget_demographic_group.data_point_date >= ? AND 
                        hc_widget_demographic_group.data_point_date <= ?) AND 
                        hc_widget_demographic_group.group_id = ? AND 
                        hc_widget_demographic_group.group_type IN (?)
                  GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return mixed[]
     * @throws DBALException
     */
    public function findLeaderCountForDatePeriodByGroupTypes(
        string $from,
        string $to,
        int $mainGroupId,
        array $groupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, SUM(m_count_leader) as m, SUM(f_count_leader) as f, SUM(u_count_leader) as u 
                  FROM hc_widget_demographic_group 
                  WHERE (hc_widget_demographic_group.data_point_date >= ? AND 
                        hc_widget_demographic_group.data_point_date <= ?) AND 
                        hc_widget_demographic_group.group_id = ? AND 
                        hc_widget_demographic_group.group_type IN (?) 
                  GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return mixed[]
     * @throws DBALException
     */
    public function findAllGenderTotalCountForDatePeriodByGroupType(
        string $from,
        string $to,
        int $mainGroupId,
        array $groupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT data_point_date, 
                        (SUM(m_count) + SUM(m_count_leader)) as m, 
                        (SUM(f_count) + SUM(f_count_leader)) as f,
                        (SUM(u_count) + SUM(u_count_leader)) as u 
                  FROM hc_widget_demographic_group 
                  WHERE (hc_widget_demographic_group.data_point_date >= ? AND 
                        hc_widget_demographic_group.data_point_date <= ?) AND 
                        hc_widget_demographic_group.group_id = ? AND 
                        hc_widget_demographic_group.group_type IN (?)
                  GROUP BY data_point_date ORDER BY data_point_date ASC;",
            [$from, $to, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }
}

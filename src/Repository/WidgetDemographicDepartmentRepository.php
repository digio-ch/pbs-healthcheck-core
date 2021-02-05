<?php

namespace App\Repository;

use App\Entity\WidgetDemographicDepartment;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class WidgetDemographicDepartmentRepository extends AggregatedEntityRepository
{
    /**
     * WidgetDemographicDepartmentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetDemographicDepartment::class);
    }

    /**
     * @param string $date
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return array|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findMembersCountForDateAndGroupType(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT birthyear, SUM(m_count) as m, SUM(f_count) as f
                    FROM hc_widget_demographic_department
                    WHERE hc_widget_demographic_department.data_point_date = ? AND 
                        hc_widget_demographic_department.group_id = ? AND
                        hc_widget_demographic_department.group_type IN (?)    
                    GROUP BY birthyear;",
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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findLeadersCountForDateAndGroupType(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT birthyear, SUM(m_count_leader) as m, SUM(f_count_leader) as f
                    FROM hc_widget_demographic_department
                    WHERE hc_widget_demographic_department.data_point_date = ? AND 
                        hc_widget_demographic_department.group_id = ? AND
                        hc_widget_demographic_department.group_type IN (?)    
                    GROUP BY birthyear;",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    public function findUnknownGenderMemberCount(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(u_count) as u
                    FROM hc_widget_demographic_department
                    WHERE hc_widget_demographic_department.data_point_date = ? AND 
                        hc_widget_demographic_department.group_id = ? AND
                        hc_widget_demographic_department.group_type IN (?);",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    public function findUnknownGenderLeaderCount(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(u_count_leader) as u
                    FROM hc_widget_demographic_department
                    WHERE hc_widget_demographic_department.data_point_date = ? AND 
                        hc_widget_demographic_department.group_id = ? AND
                        hc_widget_demographic_department.group_type IN (?);",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }
}

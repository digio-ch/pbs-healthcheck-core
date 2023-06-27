<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedDemographicDepartment;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class AggregatedDemographicDepartmentRepository extends AggregatedEntityRepository
{
    /**
     * AggregatedDemographicDepartmentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedDemographicDepartment::class);
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
                    FROM hc_aggregated_demographic_department
                    WHERE hc_aggregated_demographic_department.data_point_date = ? AND 
                        hc_aggregated_demographic_department.group_id = ? AND
                        hc_aggregated_demographic_department.group_type IN (?)    
                    GROUP BY birthyear;",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAllAssociative();
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
                    FROM hc_aggregated_demographic_department
                    WHERE hc_aggregated_demographic_department.data_point_date = ? AND 
                        hc_aggregated_demographic_department.group_id = ? AND
                        hc_aggregated_demographic_department.group_type IN (?)    
                    GROUP BY birthyear;",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAllAssociative();
    }

    public function findUnknownGenderMemberCount(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(u_count) as u
                    FROM hc_aggregated_demographic_department
                    WHERE hc_aggregated_demographic_department.data_point_date = ? AND 
                        hc_aggregated_demographic_department.group_id = ? AND
                        hc_aggregated_demographic_department.group_type IN (?);",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAllAssociative();
    }

    public function findUnknownGenderLeaderCount(string $date, int $mainGroupId, array $groupTypes)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(u_count_leader) as u
                    FROM hc_aggregated_demographic_department
                    WHERE hc_aggregated_demographic_department.data_point_date = ? AND 
                        hc_aggregated_demographic_department.group_id = ? AND
                        hc_aggregated_demographic_department.group_type IN (?);",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAllAssociative();
    }
}

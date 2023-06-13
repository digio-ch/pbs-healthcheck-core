<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedDemographicCamp;
use App\Entity\Aggregated\AggregatedDemographicCampGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class AggregatedDemographicCampGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedDemographicCampGroup::class);
    }

    /**
     * @param AggregatedDemographicCamp $camp
     * @param int $mainGroupId
     * @param string $groupType
     * @return array|false|mixed
     * @throws DBALException
     */
    public function getMembersCountByCampAndGroupType(AggregatedDemographicCamp $camp, int $mainGroupId, string $groupType)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count + f_count + u_count)
                  FROM hc_aggregated_demographic_camp_group
                  WHERE demographic_camp_id = ?
                    AND hc_aggregated_demographic_camp_group.group_id = ?
                    AND hc_aggregated_demographic_camp_group.group_type = ?;",
            [$camp->getId(), $mainGroupId, $groupType],
            [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::STRING]
        );
        return $statement->fetchOne();
    }

    /**
     * @param AggregatedDemographicCamp $camp
     * @param int $mainGroupId
     * @param string $groupType
     * @return array|false|mixed
     * @throws DBALException
     */
    public function getLeadersCountByCampAndGroupType(AggregatedDemographicCamp $camp, int $mainGroupId, string $groupType)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count_leader + f_count_leader + u_count_leader)
                  FROM hc_aggregated_demographic_camp_group
                  WHERE demographic_camp_id = ?
                    AND hc_aggregated_demographic_camp_group.group_id = ?
                    AND hc_aggregated_demographic_camp_group.group_type = ?;",
            [$camp->getId(), $mainGroupId, $groupType],
            [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::STRING]
        );
        return $statement->fetchOne();
    }

    /**
     * @param AggregatedDemographicCamp $camp
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return array|false|mixed
     * @throws DBALException
     */
    public function getAdditionalLeadersCountByCampAndGroupTypes(
        AggregatedDemographicCamp $camp,
        int $mainGroupId,
        array $groupTypes
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count_leader + f_count_leader + u_count_leader) 
                  FROM hc_aggregated_demographic_camp_group
                  WHERE demographic_camp_id = ? 
                    AND hc_aggregated_demographic_camp_group.group_type IN (?)
                    AND hc_aggregated_demographic_camp_group.group_id = ?;",
            [$camp->getId(), $groupTypes, $mainGroupId],
            [ParameterType::INTEGER, Connection::PARAM_INT_ARRAY, ParameterType::INTEGER]
        );
        return $statement->fetchAssociative();
    }

    public function deleteAllByCampGroupAndGroupType(int $campId, int $groupId, string $groupType)
    {
        $conn = $this->_em->getConnection();
        $conn->executeQuery(
            "DELETE FROM hc_aggregated_demographic_camp_group
                  WHERE demographic_camp_id = ? 
                    AND group_type = ?
                    AND group_id = ?;",
            [$campId, $groupType, $groupId],
            [ParameterType::INTEGER, ParameterType::INTEGER, ParameterType::STRING]
        );
    }
}

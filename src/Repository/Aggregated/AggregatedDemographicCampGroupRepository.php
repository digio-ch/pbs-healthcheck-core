<?php

declare(strict_types=1);

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedDemographicCamp;
use App\Entity\Aggregated\AggregatedDemographicCampGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AggregatedDemographicCampGroup>
 */
class AggregatedDemographicCampGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedDemographicCampGroup::class);
    }

    /**
     * @return array|false|mixed
     * @throws Exception
     */
    public function getMembersCountByCampAndGroupType(AggregatedDemographicCamp $camp, int $mainGroupId, string $groupType)
    {
        $conn = $this->getEntityManager()->getConnection();
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
     * @return array|false|mixed
     * @throws Exception
     */
    public function getLeadersCountByCampAndGroupType(AggregatedDemographicCamp $camp, int $mainGroupId, string $groupType)
    {
        $conn = $this->getEntityManager()->getConnection();
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
     * @return array|false|mixed
     * @throws Exception
     */
    public function getAdditionalLeadersCountByCampAndGroupTypes(
        AggregatedDemographicCamp $camp,
        int $mainGroupId,
        array $groupTypes
    ) {
        $conn = $this->getEntityManager()->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count_leader + f_count_leader + u_count_leader) 
                  FROM hc_aggregated_demographic_camp_group
                  WHERE demographic_camp_id = ? 
                    AND hc_aggregated_demographic_camp_group.group_type IN (?)
                    AND hc_aggregated_demographic_camp_group.group_id = ?;",
            [$camp->getId(), $groupTypes, $mainGroupId],
            [ParameterType::INTEGER, ArrayParameterType::INTEGER, ParameterType::INTEGER]
        );
        return $statement->fetchAssociative();
    }
}

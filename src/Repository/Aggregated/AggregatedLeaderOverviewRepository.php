<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedLeaderOverview;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class AggregatedLeaderOverviewRepository extends AggregatedEntityRepository
{
    /**
     * AggregatedLeaderOverviewRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedLeaderOverview::class);
    }

    /**
     * @param int $mainGroupId
     * @param array $groupTypes
     * @param string $date
     * @return array|bool|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findMaleFemaleMembersCountForGroupTypeAndDate(int $mainGroupId, array $groupTypes, string $date)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT SUM(m_count) as m, SUM(f_count) as f, SUM(u_count) as u
                    FROM hc_aggregated_leader_overview
                    WHERE hc_aggregated_leader_overview.data_point_date = ? AND 
                          hc_aggregated_leader_overview.group_id = ? AND
                          hc_aggregated_leader_overview.group_type IN (?);",
            [$date, $mainGroupId, $groupTypes],
            [ParameterType::STRING, ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAssociative();
    }
}

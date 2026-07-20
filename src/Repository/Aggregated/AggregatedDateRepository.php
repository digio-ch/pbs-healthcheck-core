<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedDate;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;

class AggregatedDateRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedDate::class);
    }

    /**
     * Gathers latest data point date for the given group
     *
     * @param int $groupId
     * @return ?DateTimeInterface
     */
    public function findLatestDataPointDateByGroupId(int $groupId): ?DateTimeInterface
    {
        $result = $this->createQueryBuilder('widget')
            ->select('widget.dataPointDate')
            ->where('widget.group = :groupId')
            ->addOrderBy('widget.dataPointDate', 'DESC')
            ->setParameter('groupId', $groupId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($result)) {
            return null;
        }

        return $result['dataPointDate'];
    }

    /**
     * Gathers all data point dates for the given groups
     * The dates are returned as string (2026-01-01)
     *
     * @param int[] $groupIds
     * @return string[]
     */
    public function findDataPointDatesByGroupIds($groupIds): array
    {
        $result = $this->createQueryBuilder('widget')
            ->select('widget.dataPointDate')
            ->distinct(true)
            ->where('widget.group IN (:groups)')
            ->addOrderBy('widget.dataPointDate', 'DESC')
            ->setParameter('groups', $groupIds)
            ->getQuery()
            ->getArrayResult();

        return array_map(
            fn(array $column) => $column['dataPointDate']->format('Y-m-d'),
            $result
        );
    }
}

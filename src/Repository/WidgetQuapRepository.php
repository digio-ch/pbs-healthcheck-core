<?php

namespace App\Repository;

use App\Entity\WidgetQuap;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WidgetQuap|null find($id, $lockMode = null, $lockVersion = null)
 * @method WidgetQuap|null findOneBy(array $criteria, array $orderBy = null)
 * @method WidgetQuap[]    findAll()
 * @method WidgetQuap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetQuapRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetQuap::class);
    }

    public function findCurrentForGroup(int $groupId): ?WidgetQuap
    {
        $data = $this->createQueryBuilder('quap')
            ->andWhere('quap.dataPointDate IS NULL')
            ->andWhere('quap.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        if (sizeof($data) > 0) {
            return $data[0];
        }
        return null;
    }
}

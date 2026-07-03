<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedPersonRole;
use App\Entity\Midata\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AggregatedPersonRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AggregatedPersonRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AggregatedPersonRole[]    findAll()
 * @method AggregatedPersonRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<AggregatedPersonRole>
 */
class AggregatedPersonRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedPersonRole::class);
    }

    /**
     * @param $start
     * @param $end
     * @return AggregatedPersonRole[]|null
     */
    public function findByGroupInTimeframe(Group $group, $start, $end)
    {
        return $this->createQueryBuilder('a')
            ->where('a.group = :group_id')
            ->andWhere('NOT ((a.end_at IS NOT NULL AND a.end_at < :start) OR a.start_at > :end)')
            ->orderBy('a.start_at')
            ->setParameter('group_id', $group->getId())
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }
}

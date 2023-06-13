<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Camp;
use App\Entity\Midata\EventDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\Persistence\ManagerRegistry;

class EventDateRepository extends ServiceEntityRepository
{
    /**
     * EventDateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventDate::class);
    }

    /**
     * @param array $subGroups
     * @return mixed
     */
    public function getMinStartAtDateForSubGroups(array $subGroups)
    {
        $conn = $this->_em->getConnection();
        return $conn->createQueryBuilder()
            ->select("MIN(ed.start_at)")
            ->from("midata_event_date", "ed")
            ->innerJoin("ed", "midata_event", "e", "e.id = ed.event_id")
            ->innerJoin("e", 'midata_event_group', 'eg', 'eg.event_id = e.id')
            ->where("e.type = 'camp'")
            ->andWhere('eg.group_id IN (:ids)')
            ->setParameter('ids', $subGroups, Connection::PARAM_INT_ARRAY)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * @param string $from
     * @param string $to
     * @param array $subGroups
     * @return mixed
     */
    public function getAllForPeriodAndSubgroups(string $from, string $to, array $subGroups)
    {
        return $this->createQueryBuilder('ed')
            ->innerJoin('ed.event', 'e')
            ->innerJoin('e.groups', 'eg')
            ->where('ed.startAt > :from')
            ->andWhere('ed.startAt < :to')
            ->andWhere('eg.group IN (:ids)')
            ->andWhere('e INSTANCE OF :class')
            ->setParameter('ids', $subGroups, Connection::PARAM_INT_ARRAY)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('class', $this->_em->getClassMetadata(Camp::class))
            ->getQuery()
            ->getResult();
    }
}

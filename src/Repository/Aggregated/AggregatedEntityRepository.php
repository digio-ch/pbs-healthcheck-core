<?php

namespace App\Repository\Aggregated;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

abstract class AggregatedEntityRepository extends ServiceEntityRepository
{
    /**
     * @param $entity
     * @throws ORMException
     */
    public function remove($entity): void
    {
        $this->_em->remove($entity);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * @param int $mainGroupId
     * @return int|mixed|string
     * @throws DBALException
     */
    public function getAllDataPointDates(int $mainGroupId)
    {
        $conn = $this->_em->getConnection();
        $statementString = "SELECT DISTINCT data_point_date, group_id FROM "
            . $this->getClassMetadata()->getTableName()
            . " WHERE group_id = ?;";
        $statement = $conn->executeQuery($statementString, [$mainGroupId], [ParameterType::INTEGER]);
        return $statement->fetchFirstColumn();
    }
}

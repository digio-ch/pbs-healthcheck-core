<?php

namespace App\Repository\Statistics;

use App\Entity\Midata\GroupType;
use App\Entity\Statistics\StatisticGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatisticGroup>
 *
 * @method StatisticGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticGroup[]    findAll()
 * @method StatisticGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatisticGroup::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(StatisticGroup $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(StatisticGroup $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function deleteAll()
    {
        $this->_em->createQueryBuilder()
            ->delete(StatisticGroup::class, 'g')
            ->getQuery()
            ->execute();
        $this->_em->flush();
        $metadata = $this->_em->getClassMetaData(StatisticGroup::class);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    public function flush()
    {
        $this->_em->flush();
    }

    /**
     * Finds all the children of the group that are group type 2,3 or 8. (Kanton, Region, Abteilung)
     * @param int $groupId
     * @return int[]
     * @throws Exception
     */
    public function findAllRelevantChildGroups(int $groupId, array $types = [GroupType::DEPARTMENT, GroupType::REGION, GroupType::CANTON]): array
    {
        $conn = $this->_em->getConnection();
        $query = $conn->executeQuery(
            "WITH RECURSIVE parent as (
                    SELECT statistic_group.*, midata_group_type.group_type as group_type
                    FROM statistic_group
                    JOIN midata_group_type ON group_type_id = midata_group_type.id
                    WHERE statistic_group.id = (?)
                    UNION
                    SELECT child.*, midata_group_type.group_type
                    FROM statistic_group child, parent p, midata_group_type
                    Where child.parent_group_id = p.id AND child.group_type_id = midata_group_type.id
                ) SELECT * from parent
                Where parent.group_type IN (?);",
            [$groupId, $types],
            [ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $query->fetchFirstColumn();
    }

    /**
     * @param int $associationId
     * @param string $date
     * @return string[]
     * @throws Exception
     */
    public function findDepartmentNames(int $associationId, string $date): array
    {
        $query = $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                "WITH RECURSIVE parent as (
                SELECT 
                    statistic_group.id, 
                    statistic_group.\"name\",
                    midata_group_type.group_type as group_type
                FROM statistic_group
                JOIN midata_group_type ON group_type_id = midata_group_type.id
                WHERE statistic_group.id = ?
                UNION
                SELECT
                    child.id,
                    child.\"name\",
                    midata_group_type.group_type
                FROM statistic_group child
                JOIN parent p ON child.parent_group_id = p.id
                JOIN midata_group_type ON child.group_type_id = midata_group_type.id
            ) SELECT DISTINCT  
                result.\"name\" 
            FROM parent result
            -- join with aggregation table to only select the ones that existed on the given date
            JOIN hc_aggregated_demographic_group ON hc_aggregated_demographic_group.group_id = result.id
            Where result.group_type = ?
            AND hc_aggregated_demographic_group.data_point_date = ?;",
                [$associationId, GroupType::DEPARTMENT, $date],
                [ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING]
            );

        return $query->fetchFirstColumn();
    }
}

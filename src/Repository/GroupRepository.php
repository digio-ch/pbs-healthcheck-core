<?php

namespace App\Repository;

use App\Entity\Group;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\DataProvider\WidgetDataProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findParentGroupById(int $groupId)
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('g.id = :id')
            ->andWhere('groupType.groupType = :type')
            ->setParameter('id', $groupId)
            ->setParameter('type', 'Group::Abteilung')
            ->getQuery()
            ->getResult();
    }

    public function findParentGroupsForPerson(int $personId)
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->join('g.personRoles', 'personRoles')
            ->join('personRoles.person', 'person')
            ->join('personRoles.role', 'role')
            ->where('groupType.groupType IN (:names)')
            ->andWhere('role.roleType IN (:roleTypes)')
            ->andWhere('person.id = :personId')
            ->andWhere('personRoles.deletedAt IS NULL')
            ->setParameter('names', [
                'Group::Abteilung',
                'Group::Kantonalverband',
                'Group::Bund',
            ], Connection::PARAM_STR_ARRAY)
            ->setParameter(
                'roleTypes',
                array_merge(WidgetAggregator::$mainGroupRoleTypes, ['Group::Abteilung::Coach']),
                Connection::PARAM_STR_ARRAY
            )
            ->setParameter('personId', $personId, ParameterType::INTEGER)
            ->getQuery()
            ->getResult();
    }

    public function findAllDepartmentalParentGroups()
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('groupType.groupType = :name')
            ->setParameter('name', 'Group::Abteilung')
            ->getQuery()
            ->getResult();
    }

    public function findAllParentGroups()
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('groupType.groupType IN (:names)')
            ->setParameter('names', [
                'Group::Abteilung',
                'Group::Kantonalverband',
                'Group::Bund',
            ])
            ->getQuery()
            ->getResult();
    }

    public function findParentGroups(array $parents)
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('groupType.groupType IN (:names)')
            ->setParameter('names', $parents)
            ->getQuery()
            ->getResult();
    }

    public function getAllSubGroupsByGroupId(int $groupId)
    {
        // todo: only fetch relevant group_types
        $conn = $this->_em->getConnection();
        $query = "WITH RECURSIVE tree AS (
              SELECT id
              FROM midata_group WHERE parent_group_id = :groupId
              UNION ALL             
              SELECT midata_group.id
              FROM midata_group, tree
              WHERE midata_group.parent_group_id = tree.id
            ) SELECT * FROM tree;
        ";

        $statement = $conn->prepare($query);
        $statement->bindValue('groupId', $groupId);
        $statement->execute();

        return $statement->fetchAll(FetchMode::COLUMN);
    }

    public function findOneByIdAndType(int $groupId, string $type)
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('groupType.groupType = :type')
            ->andWhere('g.id = :groupId')
            ->setParameter('type', $type)
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllRelevantSubGroupIdsByParentGroupId(int $groupId)
    {
        $conn = $this->_em->getConnection();
        $query = $conn->executeQuery(
            "
            WITH RECURSIVE tree AS (
              SELECT g.id FROM midata_group as g
              INNER JOIN
                  midata_group_type as gt ON g.group_type_id = gt.id
              WHERE parent_group_id = ? AND 
                    gt.group_type IN (?)
              UNION ALL
              SELECT g.id FROM midata_group as g
              INNER JOIN tree ON g.parent_group_id = tree.id
              INNER JOIN
                  midata_group_type as gt ON g.group_type_id = gt.id
              WHERE gt.group_type IN (?) AND 
                    g.parent_group_id = tree.id
            ) SELECT * FROM tree;",
            [$groupId, WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES, WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES],
            [ParameterType::STRING, Connection::PARAM_STR_ARRAY, Connection::PARAM_STR_ARRAY]
        );
        return $query->fetchAll(FetchMode::COLUMN);
    }

    public function findAllSubGroupIdsByParentGroupId(int $groupId)
    {
        $conn = $this->_em->getConnection();
        $query = $conn->executeQuery(
            "
            WITH RECURSIVE tree AS (
              SELECT g.id FROM midata_group as g
              INNER JOIN
                  midata_group_type as gt ON g.group_type_id = gt.id
              WHERE parent_group_id = ?
              UNION ALL
              SELECT g.id FROM midata_group as g
              INNER JOIN tree ON g.parent_group_id = tree.id
              INNER JOIN
                  midata_group_type as gt ON g.group_type_id = gt.id
              WHERE g.parent_group_id = tree.id
            ) SELECT * FROM tree;",
            [ $groupId ],
            [ ParameterType::STRING ]
        );
        return $query->fetchAll(FetchMode::COLUMN);
    }

    /**
     * @param string $parentGroupId
     * @param array|string[] $subGroupTypes
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findAllRelevantSubGroupsByParentGroupId(
        string $parentGroupId,
        array $subGroupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ) {
        $conn = $this->_em->getConnection();
        $query = $conn->executeQuery(
            "
            WITH RECURSIVE tree AS (
              SELECT g.id, g.group_type_id, gt.group_type FROM midata_group as g
              INNER JOIN
                  midata_group_type as gt ON g.group_type_id = gt.id
              WHERE parent_group_id = ? AND 
                    gt.group_type IN (?)
              UNION ALL
              SELECT g.id, g.group_type_id, gt.group_type FROM midata_group as g
              INNER JOIN tree ON g.parent_group_id = tree.id
              INNER JOIN
                  midata_group_type as gt ON g.group_type_id = gt.id
              WHERE gt.group_type IN (?) AND 
                    g.parent_group_id = tree.id
            ) SELECT * FROM tree;",
            [$parentGroupId, $subGroupTypes, $subGroupTypes],
            [ParameterType::STRING, Connection::PARAM_STR_ARRAY, Connection::PARAM_STR_ARRAY]
        );
        return $query->fetchAll();
    }
}

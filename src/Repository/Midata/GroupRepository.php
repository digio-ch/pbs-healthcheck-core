<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Service\Aggregator\WidgetAggregator;
use App\Service\DataProvider\WidgetDataProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
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
                'Group::Region',
                'Group::Kantonalverband',
                'Group::Bund',
            ])
            ->getQuery()
            ->getResult();
    }

    public function findAllDepartmentalAndRegionalAndCantonalGroups()
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('groupType.groupType IN (:names)')
            ->setParameter('names', [
                'Group::Region',
                'Group::Kantonalverband',
                'Group::Abteilung',
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

        return $conn->executeQuery($query, ['groupId' => $groupId])
            ->fetchFirstColumn();
    }

    public function findOneByIdAndType(int $groupId, array $types)
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'groupType')
            ->where('groupType.groupType IN (:types)')
            ->andWhere('g.id = :groupId')
            ->setParameter('types', $types)
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
        return $query->fetchFirstColumn();
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
            [$groupId],
            [ParameterType::STRING]
        );
        return $query->fetchFirstColumn();
    }

    /**
     * @param string $parentGroupId
     * @param array|string[] $subGroupTypes
     * @return array|mixed[]
     * @throws Exception
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
        return $query->fetchAllAssociative();
    }

    /**
     * returns an array of arrays containing the attributes of AggregatedQuad
     * @param int $cantonId
     * @return array<array>
     */
    public function findAllDepartmentsFromCanton(int $cantonId): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'gt')
            ->where('g.cantonId = :cantonId')
            ->andWhere('gt.groupType IN (:groupType)')
            ->setParameter('cantonId', $cantonId)
            ->setParameter('groupType', [GroupType::DEPARTMENT, GroupType::REGION])
            ->getQuery()
            ->getArrayResult();
    }

    public function findAllDepartmentsForFederation(int $federationId): array
    {
        return $this->createQueryBuilder('g')
            ->join('g.groupType', 'gt')
            ->where('g.parentGroup = :federationId')
            ->andWhere('gt.groupType IN (:groupType)')
            ->setParameter('federationId', $federationId)
            ->setParameter('groupType', ['Group::Kantonalverband'])
            ->getQuery()
            ->getArrayResult();
    }
}

<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Group;
use App\Service\DataProvider\WidgetDataProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findParentGroupById(int $groupId): mixed
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

    public function findAllParentGroups(): mixed
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

    public function findAllDepartmentalAndRegionalAndCantonalGroups(): mixed
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

    public function findOneByIdAndType(int $groupId, array $types): mixed
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

    public function findAllRelevantSubGroupIdsByParentGroupId(int $groupId): array
    {
        $conn = $this->getEntityManager()->getConnection();
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
            [ParameterType::STRING, ArrayParameterType::STRING, ArrayParameterType::STRING]
        );
        return $query->fetchFirstColumn();
    }

    public function findAllSubGroupIdsByParentGroupId(int $groupId): array
    {
        $conn = $this->getEntityManager()->getConnection();
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
     * @param array|string[] $subGroupTypes
     * @return array|mixed[]
     * @throws Exception
     */
    public function findAllRelevantSubGroupsByParentGroupId(
        string $parentGroupId,
        array $subGroupTypes = WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
    ): array {
        $conn = $this->getEntityManager()->getConnection();
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
            [ParameterType::STRING, ArrayParameterType::STRING, ArrayParameterType::STRING]
        );
        return $query->fetchAllAssociative();
    }
}

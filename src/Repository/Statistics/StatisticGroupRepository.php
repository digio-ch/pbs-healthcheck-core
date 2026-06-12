<?php

namespace App\Repository\Statistics;

use App\Entity\Midata\GroupType;
use App\Entity\Statistics\StatisticGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Id\AssignedGenerator;
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

    public function add(StatisticGroup $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatisticGroup $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteAll()
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete(StatisticGroup::class, 'g')
            ->getQuery()
            ->execute();
        $this->getEntityManager()->flush();
        $metadata = $this->getEntityManager()->getClassMetaData(StatisticGroup::class);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Finds all the children of the group that are group type 2,3 or 8. (Kanton, Region, Abteilung)
     * @param int $groupId
     * @param string[] $types
     * @return int[]
     * @throws Exception
     */
    public function findAllRelevantChildGroups(int $groupId, array $types = [GroupType::DEPARTMENT, GroupType::REGION, GroupType::CANTON]): array
    {
        $conn = $this->getEntityManager()->getConnection();
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
            [ParameterType::INTEGER, ArrayParameterType::STRING]
        );
        return $query->fetchFirstColumn();
    }
}

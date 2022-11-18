<?php

namespace App\Repository\Midata;

use App\Entity\Midata\GroupType;
use App\Service\DataProvider\WidgetDataProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class GroupTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupType::class);
    }

    public function findGroupTypesForParentGroup(int $groupId)
    {
        $connection = $this->_em->getConnection();
        $statement = $connection->executeQuery(
            "
            SELECT DISTINCT gt.group_type, gt.id, gt.de_label, gt.fr_label, gt.it_label
            FROM midata_group_type as gt
            INNER JOIN midata_group ON gt.id = midata_group.group_type_id
            WHERE midata_group.parent_group_id = ? AND 
                  gt.group_type IN (?);",
            [$groupId, WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES],
            [ParameterType::INTEGER, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }
}

<?php

namespace App\Repository\Midata;

use App\Entity\Midata\PersonRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonRole>
 */
class PersonRoleRepository extends ServiceEntityRepository
{
    /**
     * PersonRoleRepository constructor.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonRole::class);
    }

    public function findRolesForPersonInGroup(int $groupId, int $personId): mixed
    {
        return $this->createQueryBuilder('personRole')
            ->innerJoin('personRole.group', 'g')
            ->innerJoin('personRole.person', 'p')
            ->innerJoin('g.groupType', 'gt')
            ->where('p.id = :personId')
            ->andWhere('g.id = :groupId')
            ->andWhere('gt.groupType = :groupType')
            ->setParameter('personId', $personId)
            ->setParameter('groupType', 'Group::Abteilung')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }

    public function findAllPersonInGroupByRole(array $groupTypes, array $roleTypes): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT midata_person_role.person_id, midata_person_role.group_id, parent_group_id, midata_group_type.group_type
                FROM midata_person_role
                JOIN midata_role ON midata_person_role.role_id = midata_role.id
                JOIN midata_group ON midata_person_role.group_id = midata_group.id
                JOIN midata_group_type ON midata_group.group_type_id = midata_group_type.id
                WHERE midata_group_type.group_type IN (?)
                    AND midata_role.role_type IN (?)
                    AND midata_person_role.deleted_at IS NULL;",
            [
                $groupTypes,
                $roleTypes,
            ],
            [
                ArrayParameterType::STRING,
                ArrayParameterType::STRING,
            ]
        );
        return $statement->fetchAllAssociative();
    }
}

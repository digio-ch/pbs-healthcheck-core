<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function save(Permission $invite)
    {
        $this->_em->persist($invite);
        $this->_em->flush();
    }

    public function remove(Permission $invite)
    {
        $this->_em->remove($invite);
        $this->_em->flush();
    }

    public function findByPersonGroupAndPermission(int $groupId, int $personId, int $permissionTypeId): ?Permission
    {
        return $this->findOneBy([
            'group' => $groupId,
            'person' => $personId,
            'permissionType' => $permissionTypeId,
        ]);
    }

    public function findAllByGroupIdAndEmail(string $email, int $groupId)
    {
        return $this->createQueryBuilder('permission')
            ->join('permission.group', 'g')
            ->where('permission.email = :email')
            ->andWhere('g.id = :groupId')
            ->andWhere('permission.expirationDate > :now')
            ->setParameter('email', $email)
            ->setParameter('groupId', $groupId)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findByGroupId(int $groupId)
    {
        $query = $this->createQueryBuilder('permission');
        return $query
            ->join('permission.group', 'g')
            ->where('g.id = :groupId')
            ->andWhere($query->expr()->orX(
                $query->expr()->gt('permission.expirationDate', ':now'),
                $query->expr()->isNull('permission.expirationDate')
            ))
            ->setParameter('groupId', $groupId)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $id
     * @param string $email
     * @return int|mixed|string
     */
    public function findAllValidByIdOrEmail(int $id, string $email)
    {
        $query = $this->createQueryBuilder('permission');
        return $query
            ->where($query->expr()->orX(
                $query->expr()->eq('permission.person', ':person'),
                $query->expr()->eq('permission.email', ':email')
            ))
            ->andWhere($query->expr()->orX(
                $query->expr()->gt('permission.expirationDate', ':now'),
                $query->expr()->isNull('permission.expirationDate')
            ))
            ->setParameter('person', $id)
            ->setParameter('email', $email)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function insertPermission(int $groupId, int $permissionTypeId, \DateTimeImmutable $expirationDate, ?int $personId,  ?string $email): void
    {
        $conn = $this->_em->getConnection();
        $conn->executeStatement(
            "INSERT INTO hc_permission
                    (id, person_id, permission_type_id, group_id, email, expiration_date)
                    VALUES
                    (nextval('hc_permission_id_seq'), ?, ?, ?, ?, ?);",
            [
                $personId,
                $permissionTypeId,
                $groupId,
                $email,
                $expirationDate->format('Y-m-d'),
            ],
            [
                ParameterType::INTEGER,
                ParameterType::INTEGER,
                ParameterType::INTEGER,
                ParameterType::STRING,
                ParameterType::STRING,
            ]
        );
    }

    /**
     * @param Group $group
     * @param int $id
     * @param string $email
     * @return Permission|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findHighestByIdOrEmail(Group $group, int $id, string $email): ?Permission
    {
        $query = $this->createQueryBuilder('permission');

        return $query
            ->where('permission.group = :group')
            ->andWhere($query->expr()->orX(
                $query->expr()->eq('permission.person', ':person'),
                $query->expr()->eq('permission.email', ':email')
            ))
            ->andWhere($query->expr()->orX(
                $query->expr()->gt('permission.expirationDate', ':now'),
                $query->expr()->isNull('permission.expirationDate')
            ))
            ->orderBy('permission.permissionType')
            ->setMaxResults(1)
            ->setParameter('group', $group->getId())
            ->setParameter('person', $id)
            ->setParameter('email', $email)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }
}

<?php

namespace App\Repository\Security;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use App\Entity\Midata\Group;
use App\Entity\Security\Permission;
use App\Entity\Security\PermissionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function save(Permission $invite): void
    {
        $this->getEntityManager()->persist($invite);
        $this->getEntityManager()->flush();
    }

    public function persist(Permission $permission): void
    {
        $this->getEntityManager()->persist($permission);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Permission $invite): void
    {
        $this->getEntityManager()->remove($invite);
        $this->getEntityManager()->flush();
    }

    public function findByPersonGroupAndPermission(int $groupId, int $personId, int $permissionTypeId): ?Permission
    {
        return $this->findOneBy([
            'group' => $groupId,
            'person' => $personId,
            'permissionType' => $permissionTypeId,
        ]);
    }

    public function findAllByGroupIdAndEmail(string $email, int $groupId): mixed
    {
        return $this->createQueryBuilder('permission')
            ->join('permission.group', 'g')
            ->where('permission.email = :email')
            ->andWhere('g.id = :groupId')
            ->andWhere('permission.expirationDate > :now')
            ->setParameter('email', $email)
            ->setParameter('groupId', $groupId)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findByGroupId(int $groupId): mixed
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
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function findAllValidByIdOrEmail(int $id, string $email): mixed
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
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }

    public function insertPermission(int $groupId, int $permissionTypeId, ?DateTimeImmutable $expirationDate, ?int $personId, ?string $email): void
    {
        $conn = $this->getEntityManager()->getConnection();
        $conn->executeStatement(
            "INSERT INTO hc_security_permission
                    (id, person_id, permission_type_id, group_id, email, expiration_date)
                    VALUES
                    (nextval('hc_security_permission_id_seq'), ?, ?, ?, ?, ?);",
            [
                $personId,
                $permissionTypeId,
                $groupId,
                $email,
                $expirationDate instanceof DateTimeImmutable ? $expirationDate->format('Y-m-d') : null,
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
     * @throws NonUniqueResultException
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
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $email
     * @throws NonUniqueResultException
     */
    public function findHighestById(Group $group, int $id): ?Permission
    {
        $query = $this->createQueryBuilder('permission');

        return $query
            ->where('permission.group = :group')
            ->andWhere('permission.person = :person')
            ->andWhere($query->expr()->orX(
                $query->expr()->gt('permission.expirationDate', ':now'),
                $query->expr()->isNull('permission.expirationDate')
            ))
            ->orderBy('permission.permissionType')
            ->setMaxResults(1)
            ->setParameter('group', $group->getId())
            ->setParameter('person', $id)
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function endAllOpenPermissions(): void
    {
        $now = new DateTimeImmutable();

        $conn = $this->getEntityManager()->getConnection();
        $conn->executeStatement(
            "UPDATE hc_security_permission
                    SET expiration_date = ?
                    WHERE expiration_date IS NULL;",
            [
                $now->format('Y-m-d'),
            ],
            [
                ParameterType::STRING,
            ]
        );
    }

    /**
     * @return Permission[]
     * @throws Exception
     */
    public function findAllExpiringPermissionsToNotify(): array
    {
        $query = $this->createQueryBuilder('permission');

        return $query
            ->where('permission.expirationDate BETWEEN :now AND :inOneMonth')
            ->andWhere('permission.preExpiryNotified = false')
            ->setParameter('now', new DateTimeImmutable())
            ->setParameter('inOneMonth', new DateTimeImmutable('+1 month'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Permission[]
     */
    public function findActiveOwnerPermissions(int $groupId, int $personId): array
    {
        $query = $this->createQueryBuilder('permission');

        return $query
            ->join('permission.permissionType', 'type')
            ->where('permission.person = :personId')
            ->andWhere('permission.group = :groupId')
            ->andWhere('type.key = :ownerType')
            ->andWhere('permission.expirationDate IS NULL')
            ->setParameter('personId', $personId)
            ->setParameter('groupId', $groupId)
            ->setParameter('ownerType', PermissionType::OWNER)
            ->getQuery()
            ->getResult();
    }
}

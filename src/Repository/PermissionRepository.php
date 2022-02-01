<?php

namespace App\Repository;

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
        return $this->createQueryBuilder('invite')
            ->join('invite.group', 'g')
            ->where('invite.email = :email')
            ->andWhere('g.id = :groupId')
            ->andWhere('invite.expirationDate > :now')
            ->setParameter('email', $email)
            ->setParameter('groupId', $groupId)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function findByGroupId(int $groupId)
    {
        return $this->createQueryBuilder('invite')
            ->join('invite.group', 'g')
            ->where('g.id = :groupId')
            ->andWhere('invite.expirationDate > :now')
            ->setParameter('groupId', $groupId)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $email
     * @return int|mixed|string
     */
    public function findAllValidByEmail(string $email)
    {
        return $this->createQueryBuilder('invite')
            ->where('invite.email = :email')
            ->andWhere('invite.expirationDate > :now')
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
}

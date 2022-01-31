<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    // /**
    //  * @return Invite[] Returns an array of Invite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Invite
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

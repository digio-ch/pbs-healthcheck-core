<?php

namespace App\Repository;

use App\Entity\Invite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invite[]    findAll()
 * @method Invite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invite::class);
    }

    public function save(Invite $invite)
    {
        $this->_em->persist($invite);
        $this->_em->flush();
    }

    public function remove(Invite $invite)
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

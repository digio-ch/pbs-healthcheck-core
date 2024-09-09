<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\GamificationPersonProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GamificationPersonProfile>
 *
 * @method GamificationPersonProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method GamificationPersonProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method GamificationPersonProfile[]    findAll()
 * @method GamificationPersonProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamificationPersonProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GamificationPersonProfile::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GamificationPersonProfile $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(GamificationPersonProfile $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PersonGoal[] Returns an array of PersonGoal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PersonGoal
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

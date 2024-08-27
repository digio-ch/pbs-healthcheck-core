<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\Login;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Login>
 *
 * @method Login|null find($id, $lockMode = null, $lockVersion = null)
 * @method Login|null findOneBy(array $criteria, array $orderBy = null)
 * @method Login[]    findAll()
 * @method Login[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Login::class);
    }

    public function findAllActiveBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        $criteria['hashed_id'] = null;
        return $this->findBy($criteria,$orderBy, $limit, $offset);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Login $entity, bool $flush = true): void
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
    public function remove(Login $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function pseudonymizeAllOlderThan18Months(callable $hashFunc)
    {
        $thresholdDate = new DateTime('-18 months');
        $entities = $this->createQueryBuilder('e')
            ->where('e.date < :thresholdDate')
            ->setParameter('thresholdDate', $thresholdDate)
            ->getQuery()
            ->getResult();
        foreach ($entities as $entity) {
            $hashedId = $hashFunc($entity->getPerson()->getId());
            $entity->setPerson(null);
            $entity->setHashedPersonId($hashedId);
            $this->_em->persist($entity);
        }
        $this->_em->flush();
        return $entities;
    }

    // /**
    //  * @return Login[] Returns an array of Login objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Login
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

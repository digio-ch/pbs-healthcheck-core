<?php

namespace App\Repository\Statistics;

use App\Entity\Statistics\GroupGeoLocation;
use App\Entity\Statistics\StatisticGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupGeoLocation>
 *
 * @method GroupGeoLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupGeoLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupGeoLocation[]    findAll()
 * @method GroupGeoLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupGeoLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupGeoLocation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GroupGeoLocation $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function deleteAll()
    {
        $this->_em->createQueryBuilder()
            ->delete(GroupGeoLocation::class, 'g')
            ->getQuery()
            ->execute();
        $this->_em->flush();
        $metadata = $this->_em->getClassMetaData(GroupGeoLocation::class);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(GroupGeoLocation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function flush()
    {
        $this->_em->flush();
    }

    // /**
    //  * @return GroupGeoLocation[] Returns an array of GroupGeoLocation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupGeoLocation
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

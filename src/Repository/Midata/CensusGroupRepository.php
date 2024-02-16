<?php

namespace App\Repository\Midata;

use App\Entity\Midata\CensusGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CensusGroup>
 *
 * @method CensusGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CensusGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CensusGroup[]    findAll()
 * @method CensusGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CensusGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CensusGroup::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(CensusGroup $entity, bool $flush = true): void
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
    public function remove(CensusGroup $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getLatestYear(): int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('max', 'max', 'integer');
        $query = $this->_em->createNativeQuery('SELECT MAX(year) FROM census_group;', $rsm);
        $result = $query->getSingleScalarResult();
        if (is_null($result)) {
            throw new \Exception("No date found in census table.");
        }
        return $result;
    }

    // /**
    //  * @return CensusGroup[] Returns an array of CensusGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CensusGroup
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

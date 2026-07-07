<?php

namespace App\Repository\Midata;

use App\Entity\Midata\CensusGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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

    public function add(CensusGroup $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CensusGroup $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function getLatestYear(): int
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('max', 'max', 'integer');
        $query = $this->getEntityManager()->createNativeQuery('SELECT MAX(year) FROM census_group;', $rsm);
        $result = $query->getSingleScalarResult();
        if (is_null($result)) {
            throw new Exception("No date found in census table.");
        }
        return $result;
    }
}

<?php

namespace App\Repository\Statistics;

use App\Entity\Statistics\GroupGeoLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;
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

    public function add(GroupGeoLocation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteAll()
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete(GroupGeoLocation::class, 'g')
            ->getQuery()
            ->execute();
        $this->getEntityManager()->flush();
        $metadata = $this->getEntityManager()->getClassMetaData(GroupGeoLocation::class);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    public function remove(GroupGeoLocation $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}

<?php

namespace App\Repository\Overview;

use App\Entity\Overview\OverviewShared;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OverviewShared|null find($id, $lockMode = null, $lockVersion = null)
 * @method OverviewShared|null findOneBy(array $criteria, array $orderBy = null)
 * @method OverviewShared[]    findAll()
 * @method OverviewShared[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OverviewSharedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OverviewShared::class);
    }

    public function findByGroupId(int $groupId): ?OverviewShared
    {
        return $this->createQueryBuilder("o")
            ->where('o.groupId =:groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(OverviewShared $overviewShared)
    {
        $em = $this->getEntityManager();
        $em->persist($overviewShared);
        $em->flush();
    }

    public function remove(OverviewShared $overviewShared)
    {
        $em = $this->getEntityManager();
        $em->remove($overviewShared);
        $em->flush();
    }
}

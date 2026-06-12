<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\LevelAccess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LevelAccess>
 *
 * @method LevelAccess|null find($id, $lockMode = null, $lockVersion = null)
 * @method LevelAccess|null findOneBy(array $criteria, array $orderBy = null)
 * @method LevelAccess[]    findAll()
 * @method LevelAccess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelAccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LevelAccess::class);
    }

    public function add(LevelAccess $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

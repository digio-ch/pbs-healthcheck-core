<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\GamificationPersonProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function add(GamificationPersonProfile $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GamificationPersonProfile $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

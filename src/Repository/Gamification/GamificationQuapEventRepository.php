<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Midata\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GamificationQuapEvent>
 *
 * @method GamificationQuapEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method GamificationQuapEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method GamificationQuapEvent[]    findAll()
 * @method GamificationQuapEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamificationQuapEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GamificationQuapEvent::class);
    }

    public function add(GamificationQuapEvent $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GamificationQuapEvent $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUniqueIds(Person $person)
    {
        return $this->createQueryBuilder('e')
            ->join('e.questionnaire', 'q')
            ->select('e.aspect_local_id, q.type')
            ->where('e.person = :person')
            ->groupBy('e.aspect_local_id')
            ->addGroupBy('q.id')
            ->setParameter('person', $person)
            ->orderBy('e.aspect_local_id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

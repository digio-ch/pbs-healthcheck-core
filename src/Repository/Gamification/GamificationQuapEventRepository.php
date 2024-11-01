<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Midata\Person;
use App\Entity\Quap\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GamificationQuapEvent $entity, bool $flush = true): void
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
    public function remove(GamificationQuapEvent $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getUniquieIds(Person $person)
    {
        return $this->createQueryBuilder('e')
            ->join('e.questionnaire', 'q')
            ->select('e.local_change_index, q.type')
            ->where('e.person = :person')
            ->groupBy('e.local_change_index')
            ->addGroupBy('q.id')
            ->setParameter('person', $person)
            ->orderBy('e.local_change_index', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return GamificationQuapEvent[] Returns an array of GamificationQuapEvent objects
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
    public function findOneBySomeField($value): ?GamificationQuapEvent
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

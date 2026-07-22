<?php

namespace App\Repository\Gamification;

use App\Entity\Gamification\Login;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function add(Login $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Login $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Pseudonymizes all login entries that
     *  - are older than 18 months
     *  - aren't already pseudonymized (hashed_person_id is null)
     *  - have a person (person_id not null)
     *
     * @param callable<int, string> $hashFunc
     * @return Login[] pseudonymized logins
     */
    public function pseudonymizeAllOlderThan18Months(callable $hashFunc): array
    {
        $thresholdDate = new DateTime('-18 months');
        /**
         * @var $entities Login[]
         */
        $entities = $this->createQueryBuilder('e')
            ->where('e.date < :thresholdDate')
            ->andWhere('e.hashed_person_id IS NULL')
            ->andWhere('e.person IS NOT NULL')
            ->setParameter('thresholdDate', $thresholdDate)
            ->getQuery()
            ->getResult();

        foreach ($entities as $entity) {
            $hashedId = $hashFunc($entity->getPerson()->getId());
            $entity->setPerson(null);
            $entity->setHashedPersonId($hashedId);
            $this->getEntityManager()->persist($entity);
        }

        $this->getEntityManager()->flush();
        return $entities;
    }
}

<?php

namespace App\Repository\General;

use App\Entity\General\PersonSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonSettings>
 *
 * @method PersonSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonSettings[]    findAll()
 * @method PersonSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonSettings::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function upsert(PersonSettings $entity, bool $flush = true): ?PersonSettings
    {
        $filter = $this->findByGroupIDAndPersonID($entity->getGroup()->getId(), $entity->getPerson()->getId());

        if (is_null($filter)) {
            $this->add($entity);
            $filter = $this->findByGroupIDAndPersonID($entity->getGroup()->getId(), $entity->getPerson()->getId());
        } else {
            $filter->setCensusFilterRoles($entity->getCensusFilterRoles());
            $filter->setCensusFilterGroups($entity->getCensusFilterGroups());
            $filter->setCensusFilterMales($entity->getCensusFilterMales());
            $filter->setCensusFilterFemales($entity->getCensusFilterFemales());
        }

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $filter;
    }

    public function add(PersonSettings $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PersonSettings $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByGroupIDAndPersonID(int $groupID, int $personID): ?PersonSettings
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.group = :groupID')
            ->andWhere('p.person = :personID')
            ->setParameter('groupID', $groupID)
            ->setParameter('personID', $personID)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function removeByGroupIDAndPersonID(int $groupID, int $personID): void
    {
        $this->createQueryBuilder('p')
            ->delete('App\Entity\General\PersonSettings', 'p')
            ->andWhere('p.group = :groupID')
            ->andWhere('p.person = :personID')
            ->setParameter('groupID', $groupID)
            ->setParameter('personID', $personID)
            ->getQuery()
            ->execute();
    }
}

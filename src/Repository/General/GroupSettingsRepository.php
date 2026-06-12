<?php

namespace App\Repository\General;

use App\Entity\General\GroupSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupSettings>
 *
 * @method GroupSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupSettings[]    findAll()
 * @method GroupSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupSettings::class);
    }

    public function add(GroupSettings $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GroupSettings $entity, bool $flush = true): void
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

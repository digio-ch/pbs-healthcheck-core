<?php

namespace App\Repository;

use App\Entity\Aspect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Aspect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aspect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aspect[]    findAll()
 * @method Aspect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AspectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aspect::class);
    }

    public function getExisting(int $questionnaireId, \DateTime $dateTime)
    {

        return $this->createQueryBuilder("a")
            ->where("a.questionnaire = :questionnaireId")
            ->andWhere('((a.deleted_at IS NULL OR a.deleted_at >= :date) AND a.created_at <= :date)')
            ->setParameter("questionnaireId", $questionnaireId)
            ->setParameter("date", $dateTime)
            ->getQuery()
            ->getResult();

    }
}

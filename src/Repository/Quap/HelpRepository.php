<?php

namespace App\Repository\Quap;

use App\Entity\Quap\Help;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Help|null find($id, $lockMode = null, $lockVersion = null)
 * @method Help|null findOneBy(array $criteria, array $orderBy = null)
 * @method Help[]    findAll()
 * @method Help[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Help::class);
    }

    public function getExisting(int $questionId, string $dateTime)
    {
        return $this->createQueryBuilder("h")
            ->where("h.question = :questionId")
            ->andWhere('((h.deletedAt IS NULL OR h.deletedAt >= :date) AND h.createdAt <= :date)')
            ->setParameter("questionId", $questionId)
            ->setParameter("date", $dateTime)
            ->getQuery()
            ->getResult();
    }
}

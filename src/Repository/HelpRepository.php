<?php


namespace App\Repository;


use App\Entity\Help;
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

    public function getExisting(int $questionId, \DateTime $dateTime)
    {
        return $this->createQueryBuilder("h")
            ->where("h.question = :questionId")
            ->andWhere('((h.deleted_at IS NULL OR h.deleted_at >= :date) AND h.created_at <= :date)')
            ->setParameter("questionId", $questionId)
            ->setParameter("date", $dateTime)
            ->getQuery()
            ->getResult();
    }
}

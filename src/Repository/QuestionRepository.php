<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function getExisting(int $aspectId, string $dateTime)
    {
        return $this->createQueryBuilder("q")
            ->where("q.aspect = :aspectId")
            ->andWhere('((q.deletedAt IS NULL OR q.deletedAt >= :date) AND q.createdAt <= :date)')
            ->setParameter("aspectId", $aspectId)
            ->setParameter("date", $dateTime)
            ->getQuery()
            ->getResult();
    }
}

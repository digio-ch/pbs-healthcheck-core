<?php

namespace App\Repository\Quap;

use App\Entity\Quap\Question;
use App\Entity\Quap\Questionnaire;
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

    public function findEvaluable(): array
    {
        return $this->createQueryBuilder("q")
            ->where("q.evaluation_function IS NOT NULL")
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Questionnaire $questionnaire
     * @return Question[]
     */
    public function findEvaluableByQuestionnaire(Questionnaire $questionnaire): array
    {
        return $this->createQueryBuilder('q')
            ->join('q.aspect', 'a')
            ->where('a.questionnaire = :questionnaireID')
            ->andWhere("q.evaluation_function IS NOT NULL")
            ->setParameter('questionnaireID', $questionnaire->getId())
            ->getQuery()
            ->getResult();
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

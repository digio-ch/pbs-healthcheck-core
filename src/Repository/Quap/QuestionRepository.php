<?php

namespace App\Repository\Quap;

use App\Entity\Quap\Question;
use App\Entity\Quap\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Question::class, 'q');
        $rsm->addFieldResult('q', 'id', 'id');
        $rsm->addMetaResult('q', 'aspect_id', 'aspect');
        $rsm->addMetaResult('q', 'local_id', 'local_id');
        $rsm->addFieldResult('q', 'question_de', 'question_de');
        $rsm->addFieldResult('q', 'question_fr', 'question_fr');
        $rsm->addFieldResult('q', 'question_it', 'question_it');
        $rsm->addFieldResult('q', 'answer_options', 'answer_options');
        $rsm->addFieldResult('q', 'created_at', 'createdAt');
        $rsm->addFieldResult('q', 'deleted_at', 'deletedAt');
        $rsm->addFieldResult('q', 'evaluation_function', 'evaluation_function');
        $query = $this->_em->createNativeQuery(
            "SELECT
                    *
                FROM
                    hc_quap_question AS q
                WHERE
                    q.aspect_id = ?
                    AND((q.deleted_at IS NULL
                        OR q.deleted_at >= ?)
                    AND q.created_at::date <= ?);",
            $rsm
        );
        $query->setParameter(1, $aspectId);
        $query->setParameter(2, $dateTime);
        $query->setParameter(3, $dateTime);

        return $query->getResult();
    }
}

<?php

namespace App\Repository\Quap;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Questionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Questionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Questionnaire[]    findAll()
 * @method Questionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Questionnaire::class);
    }

    public function getQuestionnaireByGroup(Group $group): Questionnaire
    {
        if (in_array($group->getGroupType()->getGroupType(), [GroupType::CANTON, GroupType::REGION])) {
            $questionnaireType = Questionnaire::TYPE_CANTON;
        } else {
            $questionnaireType = Questionnaire::TYPE_DEPARTMENT;
        }
        return $this->createQueryBuilder('q')
            ->where('q.type = :type')
            ->setParameter('type', $questionnaireType)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Returns an array that maps the questionnaire type to the amount of aspects that can be answered manually.
     *
     * Aspects that have at least one question that has no evaluation_function are considered manually answerable.
     * @return array<string,int>
     * @throws Exception
     */
    public function getAmountOfAnswerableAspects(): array
    {
        $conn = $this->_em->getConnection();
        $query = $conn->executeQuery(
            "SELECT \"type\", COUNT(*) FROM (
                    SELECT 
                        hc_quap_questionnaire.\"type\",
                        BOOL_AND(
                            hc_quap_question.evaluation_function IS NOT NULL
                        ) as automatic
                    FROM hc_quap_questionnaire
                    JOIN hc_quap_aspect ON hc_quap_aspect.questionnaire_id = hc_quap_questionnaire.id
                    JOIN hc_quap_question ON hc_quap_question.aspect_id = hc_quap_aspect.id
                    GROUP BY hc_quap_questionnaire.id, hc_quap_questionnaire.\"type\", hc_quap_aspect.id
                ) as p
                WHERE automatic = FALSE
                GROUP BY \"type\";",
        );
        return $query->fetchAllKeyValue();
    }
}

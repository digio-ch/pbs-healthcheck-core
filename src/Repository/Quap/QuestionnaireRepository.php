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
     * Returns an array that maps the questionnaire type to an array of local_aspect_ids of aspects that can be answered manually.
     *
     * Aspects that have been deleted are ignored.
     *
     * Aspects that have at least one existing question that has no evaluation_function are considered manually answerable.
     * @return array<string,int[]>
     * @throws Exception
     */
    public function getExistingAnswerableAspects(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $query = $conn->executeQuery(
            'SELECT "type", JSON_AGG(local_aspect_id) as aspects FROM (
                    SELECT
                        hc_quap_questionnaire."type",
                        hc_quap_aspect.local_id as local_aspect_id,
                        BOOL_AND(
                            hc_quap_question.evaluation_function IS NOT NULL
                        ) as automatic
                    FROM hc_quap_questionnaire
                    JOIN hc_quap_aspect ON hc_quap_aspect.questionnaire_id = hc_quap_questionnaire.id
                    JOIN hc_quap_question ON hc_quap_question.aspect_id = hc_quap_aspect.id
                    WHERE hc_quap_aspect.deleted_at IS NULL
                    AND hc_quap_question.deleted_at IS NULL
                    GROUP BY hc_quap_questionnaire.id, hc_quap_questionnaire."type", hc_quap_aspect.id, hc_quap_aspect.local_id
                ) as p
                WHERE automatic = FALSE
                GROUP BY "type";',
        );

        $res = [Questionnaire::TYPE_DEPARTMENT => [], Questionnaire::TYPE_CANTON => []];

        // convert the aspects "{0,1,2,..}" to an array [0,1,2,...]
        foreach ($query->fetchAllAssociative() as $row) {
            $res[$row['type']] = json_decode($row['aspects']);
        }

        return $res;
    }
}

<?php

namespace App\Repository\Quap;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        if(in_array($group->getGroupType()->getGroupType(), [GroupType::CANTON, GroupType::REGION])) {
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
}

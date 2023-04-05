<?php

namespace App\Repository\Midata;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Quap\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Questionnaire::class);
    }

    public function getQuestionnaireByGroup(Group $group): Questionnaire
    {
        if($group->getGroupType()->getGroupType() == GroupType::CANTON || $group->getGroupType()->getGroupType() == GroupType::REGION) {
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

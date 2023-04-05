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
        return $this->createQueryBuilder('q')
            ->where('q.type = :type')
            ->setParameter('type', $group->getGroupType()->getGroupType() == GroupType::CANTON ? Questionnaire::TYPE_CANTON : Questionnaire::TYPE_DEPARTMENT)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}

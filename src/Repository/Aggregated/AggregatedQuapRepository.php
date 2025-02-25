<?php

namespace App\Repository\Aggregated;

use App\Entity\Aggregated\AggregatedQuap;
use App\Entity\Midata\Group;
use App\Entity\Quap\Questionnaire;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AggregatedQuap|null find($id, $lockMode = null, $lockVersion = null)
 * @method AggregatedQuap|null findOneBy(array $criteria, array $orderBy = null)
 * @method AggregatedQuap[]    findAll()
 * @method AggregatedQuap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AggregatedQuapRepository extends AggregatedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AggregatedQuap::class);
    }

    public function findCurrentForGroup(int $groupId): ?AggregatedQuap
    {
        return $this->createQueryBuilder('quap')
            ->andWhere('quap.dataPointDate IS NULL')
            ->andWhere('quap.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array $groupIds
     * @param string|null $date
     * @return AggregatedQuap[]
     */
    public function findAllAnswers(array $groupIds, ?string $date): array
    {
        $query = $this->createQueryBuilder('quap')
            ->andWhere('quap.group IN (:groupIds)')
            ->andWhere('quap.allowAccess = TRUE');

        if (is_null($date)) {
            $query = $query
                ->andWhere('quap.dataPointDate IS NULL');
        } else {
            $query = $query
                ->andWhere('quap.dataPointDate = :date')
                ->setParameter('date', $date);
        }

        return $query
            ->setParameter('groupIds', $groupIds)
            ->getQuery()
            ->getResult();
    }

    public function save(AggregatedQuap $widgetQuap): void
    {
        $this->getEntityManager()->persist($widgetQuap);
        $this->getEntityManager()->flush();
    }

    public function getQuestionnaireByGroup(Group $group): Questionnaire
    {
        $qr = $this->createQueryBuilder('w')
            ->join('w.group', 'g')
            ->where('g.id = :groupID')
            ->setParameter('groupID', $group->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return $qr[0]->getQuestionnaire();
    }
}

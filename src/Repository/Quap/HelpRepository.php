<?php

namespace App\Repository\Quap;

use App\Entity\Quap\Help;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function getExisting(int $questionId, string $dateTime)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Help::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addMetaResult('a', 'question_id', 'question');
        $rsm->addFieldResult('a', 'help_de', 'help_de');
        $rsm->addFieldResult('a', 'help_fr', 'help_fr');
        $rsm->addFieldResult('a', 'help_it', 'help_it');
        $rsm->addFieldResult('a', 'severity', 'severity');
        $rsm->addFieldResult('a', 'created_at', 'createdAt');
        $rsm->addFieldResult('a', 'deleted_at', 'deletedAt');
        $query = $this->_em->createNativeQuery(
            "SELECT
                    *
                FROM
                    hc_quap_help AS q
                WHERE
                    q.question_id = ?
                    AND((q.deleted_at IS NULL
                        OR q.deleted_at >= ?)
                    AND q.created_at::date <= ?);",
            $rsm
        );
        $query->setParameter(1, $questionId);
        $query->setParameter(2, $dateTime);
        $query->setParameter(3, $dateTime);

        return $query->getResult();
    }
}

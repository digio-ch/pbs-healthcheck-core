<?php

namespace App\Repository\Quap;

use App\Entity\Quap\Aspect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Aspect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Aspect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Aspect[]    findAll()
 * @method Aspect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AspectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aspect::class);
    }

    public function getExisting(int $questionnaireId, string $dateTime)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Aspect::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addMetaResult('a', 'questionnaire_id', 'questionnaire');
        $rsm->addMetaResult('a', 'local_id', 'local_id');
        $rsm->addFieldResult('a', 'name_de', 'name_de');
        $rsm->addFieldResult('a', 'name_fr', 'name_fr');
        $rsm->addFieldResult('a', 'name_it', 'name_it');
        $rsm->addFieldResult('a', 'created_at', 'createdAt');
        $rsm->addFieldResult('a', 'deleted_at', 'deletedAt');
        $rsm->addFieldResult('a', 'description_de', 'descriptionDe');
        $rsm->addFieldResult('a', 'description_fr', 'descriptionFr');
        $rsm->addFieldResult('a', 'description_it', 'descriptionIt');
        $query = $this->_em->createNativeQuery(
            "SELECT
                    *
                FROM
                    hc_quap_aspect AS a
                WHERE
                    a.questionnaire_id = ?
                    AND((a.deleted_at IS NULL
                        OR a.deleted_at >= ?)
                    AND a.created_at::date <= ?);",
            $rsm
        );
        $query->setParameter(1, $questionnaireId);
        $query->setParameter(2, $dateTime);
        $query->setParameter(3, $dateTime);

        return $query->getResult();
    }
}

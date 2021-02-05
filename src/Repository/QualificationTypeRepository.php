<?php

namespace App\Repository;

use App\Entity\QualificationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class QualificationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QualificationType::class);
    }

    public function findTranslation(string $locale, int $qualificationTypeId)
    {
        $fieldPrefix = '';
        switch ($locale) {
            case str_contains($locale, 'it'):
                $fieldPrefix .= 'mqt.it_label';
                break;
            case str_contains($locale, 'fr'):
                $fieldPrefix .= 'mqt.fr_label';
                break;
            default:
                $fieldPrefix .= 'mqt.de_label';
        }
        $connection = $this->_em->getConnection();
        $statement = $connection->executeQuery(
            "
            SELECT $fieldPrefix, mqt.id
            FROM midata_qualification_type as mqt
            WHERE mqt.id = ?;",
            [$qualificationTypeId],
            [ParameterType::STRING]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }
}

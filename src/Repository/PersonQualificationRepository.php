<?php

namespace App\Repository;

use App\Entity\PersonQualification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class PersonQualificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonQualification::class);
    }

    public function getPersonQualificationByDatePoint(int $personId, string $date)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT midata_qualification_type.validity, midata_qualification_type.de_label, midata_person_qualification.start_at, midata_person_qualification.end_at, midata_person_qualification.event_origin
                  FROM midata_person_qualification 
                  INNER JOIN midata_person ON midata_person_qualification.person_id = midata_person.id
                  INNER JOIN midata_qualification_type ON midata_qualification_type.id = midata_person_qualification.qualification_type_id                   
                  WHERE midata_person_qualification.person_id = ? AND (start_at <= ? AND (end_at IS NULL OR end_at > ?));",
            [$personId, $date, $date],
            [ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING]
        );

        return $statement->fetchAll();
    }

    public function findQualificationsForPersonByDate(int $personId, string $date)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT midata_qualification_type.validity, midata_qualification_type.id, midata_person_qualification.start_at, midata_person_qualification.end_at, midata_person_qualification.event_origin
                  FROM midata_person_qualification 
                  INNER JOIN midata_person ON midata_person_qualification.person_id = midata_person.id
                  INNER JOIN midata_qualification_type ON midata_qualification_type.id = midata_person_qualification.qualification_type_id                   
                  WHERE midata_person_qualification.person_id = ? AND start_at < ?;",
            [$personId, $date],
            [ParameterType::INTEGER, ParameterType::STRING]
        );
        return $statement->fetchAll();
    }
}

<?php

namespace App\Repository;

use App\Entity\Person;
use App\Service\Aggregator\WidgetAggregator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class PersonRepository extends ServiceEntityRepository
{
    /**
     * PersonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * @param string $prevDate
     * @param string $currentDate
     * @param string $gender
     * @param array $groupIds
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findAllMembersLeftByPeriodGender(string $prevDate, string $currentDate, string $gender, array $groupIds)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT midata_person.id
                  FROM midata_person
                  INNER JOIN midata_person_role ON midata_person_role.person_id = midata_person.id
                  INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id                   
                  WHERE midata_person_role.group_id IN (?) AND 
                        midata_person.gender = ? AND 
                        (leaving_date >= ? AND leaving_date < ?) AND 
                        midata_role.role_type IN (?);",
            [$groupIds, $gender, $prevDate, $currentDate, WidgetAggregator::$memberRoleTypes],
            [Connection::PARAM_INT_ARRAY, ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $prevDate
     * @param string $currentDate
     * @param string $gender
     * @param array $groupIds
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findAllLeadersLeftByPeriodGender(
        string $prevDate,
        string $currentDate,
        string $gender,
        array $groupIds
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT midata_person.id
                  FROM midata_person
                  INNER JOIN midata_person_role ON midata_person_role.person_id = midata_person.id
                  INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id                   
                  WHERE midata_person_role.group_id IN (?) AND 
                        midata_person.gender = ? AND 
                        (leaving_date >= ? AND leaving_date < ?) AND 
                        midata_role.role_type IN (?);",
            [$groupIds, $gender, $prevDate, $currentDate, WidgetAggregator::$leadersRoleTypes],
            [Connection::PARAM_INT_ARRAY, ParameterType::STRING, ParameterType::STRING, ParameterType::STRING, Connection::PARAM_STR_ARRAY]
        );

        return $statement->fetchAll();
    }

    public function mapGeoAddress(int $personId, int $geoLocationId) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "UPDATE midata_person
            SET geo_address_id = ?
            WHERE id = ?;",
            [$geoLocationId, $personId],
            [ParameterType::INTEGER, ParameterType::INTEGER]
        );
    }

    /**
     * @param Person $person
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Person $person)
    {
        $this->getEntityManager()->persist($person);
        $this->getEntityManager()->flush();
    }
}

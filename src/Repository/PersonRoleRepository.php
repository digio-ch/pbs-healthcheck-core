<?php

namespace App\Repository;

use App\Entity\PersonRole;
use App\Service\Aggregator\WidgetAggregator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class PersonRoleRepository extends ServiceEntityRepository
{
    /**
     * PersonRoleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonRole::class);
    }

    public function findRolesForPersonInGroup(int $groupId, int $personId)
    {
        return $this->createQueryBuilder('personRole')
            ->innerJoin('personRole.group', 'g')
            ->innerJoin('personRole.person', 'p')
            ->innerJoin('g.groupType', 'gt')
            ->where('p.id = :personId')
            ->andWhere('g.id = :groupId')
            ->andWhere('gt.groupType = :groupType')
            ->setParameter('personId', $personId)
            ->setParameter('groupType', 'Group::Abteilung')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $date
     * @param array $groupIds
     * @param array $groupTypes
     * @param array $leaderRoles
     * @param array $memberRoles
     * @param array $rolePriority
     * @return array|PersonRole[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAllByDate(string $date, array $groupIds, array $groupTypes, array $leaderRoles, array $memberRoles, array $rolePriority): array
    {
        $connection = $this->_em->getConnection();
        $statement = $connection->executeQuery(
            "SELECT * FROM (
                SELECT DISTINCT
                    person_id,
                    person.nickname,
                    geo.latitude,
                    geo.longitude,
                    (
                        SELECT role_type FROM midata_person_role AS person
                            JOIN midata_role AS role ON role_id = role.id
                            WHERE person.person_id = p1.person_id
                            AND person.group_id IN (?)
                            AND role_type IN (?)
                            AND person.created_at < ?
                            AND (
                                person.deleted_at IS NULL
                                OR person.deleted_at > ?
                            )
                            ORDER BY
                                array_position(ARRAY[?]::varchar[], role_type)
                            LIMIT 1
                    ) AS group_type,
                    CASE WHEN 
                        (
                            SELECT COUNT(*) FROM midata_person_role AS person
                                JOIN midata_role AS role ON role_id = role.id
                                WHERE person.person_id = p1.person_id
                                AND person.group_id IN (?)
                                AND role_type IN (?)
                                AND person.created_at < ?
                                AND (
                                    person.deleted_at IS NULL
                                    OR person.deleted_at > ?
                                )
                        ) >= 1 THEN
                        'leaders'
                    WHEN
                    	(
                            SELECT COUNT(*) FROM midata_person_role AS person
                                JOIN midata_role AS role ON role_id = role.id
                                WHERE person.person_id = p1.person_id
                                AND person.group_id IN (?)
                                AND role_type IN (?)
                                AND person.created_at < ?
                                AND (
                                    person.deleted_at IS NULL
                                    OR person.deleted_at > ?
                                )
                        ) >= 1 THEN
                        'members'
                    ELSE
                        NULL
                    END AS role_type
                    FROM midata_person_role AS p1
                    JOIN midata_person AS person ON person.id = p1.person_id
                    LEFT JOIN admin_geo_address AS geo ON person.geo_address_id = geo.id
                    WHERE p1.group_id IN (?)
                    AND p1.created_at < ?
                    AND (
                        p1.deleted_at IS NULL
                        OR p1.deleted_at > ?
                    )
                ) AS dta
                    WHERE dta.role_type IS NOT NULL;",
            [
                $groupIds,
                $groupTypes,
                $date,
                $date,
                $rolePriority,
                $groupIds,
                $leaderRoles,
                $date,
                $date,
                $groupIds,
                $memberRoles,
                $date,
                $date,
                $groupIds,
                $date,
                $date
            ],
            [
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING
            ]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $date
     * @param array $groupIds
     * @param string $gender
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findMemberCountForPeriodByGenderGroupTypeAndGroupIds(
        string $date,
        array $groupIds,
        string $gender = ''
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT count(DISTINCT person_id) 
                  FROM midata_person_role 
                  INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                  INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                  WHERE midata_person_role.group_id IN (?)  
                    AND midata_person.gender = ? 
                    AND (created_at < ? AND (deleted_at IS NULL or deleted_at > ?)) 
                    AND midata_role.role_type IN (?)
                    AND midata_person.id not in (
                        select midata_person.id from midata_person
                        join midata_person_role on midata_person_role.person_id = midata_person.id
                        join midata_role on midata_person_role.role_id = midata_role.id
                        where midata_person.group_id in (?)
                        and midata_role.role_type in (?)
                    );",
            [$groupIds, $gender, $date, $date, WidgetAggregator::$memberRoleTypes, $groupIds, WidgetAggregator::$leadersRoleTypes],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }

    public function findAllWithRoleCountInGroup(string $endDate, array $groupIds)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT person_id, midata_person.gender as gender, (
                    SELECT DISTINCT count(midata_person_role.id) FROM midata_person_role
                            INNER JOIN midata_role mr on midata_person_role.role_id = mr.id
                                WHERE midata_person_role.person_id = mpr.person_id
                                    AND (
                                        midata_person_role.created_at < ? 
                                        AND (midata_person_role.deleted_at IS NULL OR midata_person_role.deleted_at > ?)
                                    )
                                    AND midata_person_role.group_id IN (?)
                                    AND mr.role_type IN (?)
                ) as number_of_active_roles
                FROM midata_person_role as mpr
                INNER JOIN midata_role ON mpr.role_id = midata_role.id 
                INNER JOIN midata_person ON mpr.person_id = midata_person.id
                WHERE mpr.group_id IN (?) 
                    AND (mpr.created_at < ? AND (mpr.deleted_at IS NULL or mpr.deleted_at > ?)) 
                    AND midata_role.role_type IN (?);",
            [
                $endDate,
                $endDate,
                $groupIds,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes),
                $groupIds,
                $endDate,
                $endDate,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes),
            ],
            [
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll();
    }

    public function findPersonRoles(int $personId, string $endDate, array $groupIds)
    {
        return $this->createQueryBuilder('personRole')
            ->innerJoin('personRole.person', 'person')
            ->innerJoin('personRole.group', 'g')
            ->innerJoin('personRole.role', 'role')
            ->where('person.id = :id')
            ->andWhere('g.id IN (:groupIds)')
            ->andWhere('role.roleType IN (:roles)')
            ->andWhere('(personRole.createdAt < :endDate AND (personRole.deletedAt IS NULL OR personRole.deletedAt > :endDate))')
            ->setParameter('id', $personId)
            ->setParameter('endDate', $endDate)
            ->setParameter('groupIds', $groupIds)
            ->setParameter(
                'roles',
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes)
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $date
     * @param array $groupIds
     * @param string $groupType
     * @param string $gender
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findLeaderCountForPeriodByGenderGroupTypeAndGroupIds(
        string $date,
        array $groupIds,
        string $groupType,
        string $gender = ''
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT count(DISTINCT person_id) 
                  FROM midata_person_role 
                  INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                  INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                  WHERE midata_person_role.group_id IN (?)  
                    AND (midata_person.leaving_date IS NULL OR midata_person.leaving_date > ?)
                    AND midata_person.gender = ? 
                    AND (created_at < ? AND (deleted_at IS NULL or deleted_at > ?)) 
                    AND midata_role.role_type IN (?);",
            [$groupIds, $date, $gender, $date, $date, WidgetAggregator::$leaderRoleTypesByGroupType[$groupType]],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }

    /**
     * @param array $groupIds
     * @param string $date
     * @param string $gender
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findTotalLeaderCountForGenderAllSubGroupTypesAndDate(
        array $groupIds,
        string $date,
        string $gender = ''
    ) {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT count(DISTINCT person_id) 
                  FROM midata_person_role 
                  INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                  INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                  WHERE midata_person_role.group_id IN (?)  
                    AND (midata_person.leaving_date IS NULL OR midata_person.leaving_date > ?)
                    AND midata_person.gender = ? 
                    AND (created_at < ? AND (deleted_at IS NULL or deleted_at > ?)) 
                    AND midata_role.role_type IN (?);",
            [$groupIds, $date, $gender, $date, $date, WidgetAggregator::$leadersRoleTypes],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }

    /**
     * @param array $groupIds
     * @param int $eventId
     * @param string $date
     * @return array|mixed[]
     * @throws DBALException
     */
    public function getMemberParticipants(array $groupIds, int $eventId, string $date)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT midata_person_role.person_id 
                FROM midata_person_role 
                INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                INNER JOIN midata_person_event ON midata_person_event.person_id = midata_person.id
                WHERE midata_person_event.event_id = ? 
                    AND (midata_person.leaving_date IS NULL OR midata_person.leaving_date > ?)
                    AND midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                    AND (midata_person_role.created_at < ? 
                        AND (midata_person_role.deleted_at IS NULL OR midata_person_role.deleted_at > ?)
                    );",
            [$eventId, $date, $groupIds, WidgetAggregator::$memberRoleTypes, $date, $date],
            [
                ParameterType::INTEGER,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING
            ]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }

    /**
     * @param array $groupIds
     * @param int $eventId
     * @param string $date
     * @return array|mixed[]
     * @throws DBALException
     */
    public function getLeaderParticipants(array $groupIds, int $eventId, string $date)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT midata_person_role.person_id 
                FROM midata_person_role 
                INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                INNER JOIN midata_person_event ON midata_person_event.person_id = midata_person.id
                WHERE midata_person_event.event_id = ? 
                AND midata_person_role.group_id IN (?)
                AND midata_role.role_type IN (?)
                AND (midata_person_role.created_at < ? 
                    AND (midata_person_role.deleted_at IS NULL OR midata_person_role.deleted_at > ?)
                );",
            [$eventId, $groupIds, WidgetAggregator::$leadersRoleTypes, $date, $date],
            [
                ParameterType::INTEGER,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING
            ]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }

    public function findByPersonId(int $id)
    {
        return $this->createQueryBuilder('personRole')
            ->innerJoin('personRole.person', 'person')
            ->where('person.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $date
     * @param array $groupIds
     * @param string $groupType
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findAllLeadersByDateAndGroupType(string $date, array $groupIds, string $groupType)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT midata_person.id, midata_person.gender, midata_person.nickname, midata_person.birthday
                  FROM midata_person_role 
                  INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                  INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                  WHERE midata_person_role.group_id IN (?) 
                    AND (created_at <= ? AND (deleted_at IS NULL or deleted_at > ?)) 
                    AND midata_role.role_type IN (?);",
            [$groupIds, $date, $date, WidgetAggregator::$leaderRoleTypesByGroupType[$groupType]],
            [Connection::PARAM_INT_ARRAY, ParameterType::STRING, ParameterType::STRING, Connection::PARAM_STR_ARRAY]
        );
        return $statement->fetchAll();
    }

    // demographic-department aggregation queries

    public function findAllByYearWithRoleCountInGroup(string $endDate, string $year, array $groupIds)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT person_id, extract(year from midata_person.birthday) as birthyear, midata_person.gender as gender, (
                    SELECT DISTINCT count(midata_person_role.id) FROM midata_person_role
                            INNER JOIN midata_role mr on midata_person_role.role_id = mr.id
                                WHERE midata_person_role.person_id = mpr.person_id
                                    AND (
                                        midata_person_role.created_at < ? 
                                        AND (midata_person_role.deleted_at IS NULL OR midata_person_role.deleted_at > ?)
                                    )
                                    AND midata_person_role.group_id IN (?)
                                    AND mr.role_type IN (?)
                ) as number_of_active_roles
                FROM midata_person_role as mpr
                INNER JOIN midata_role ON mpr.role_id = midata_role.id 
                INNER JOIN midata_person ON mpr.person_id = midata_person.id
                WHERE mpr.group_id IN (?) 
                    AND (midata_person.leaving_date IS NULL OR midata_person.leaving_date > ?)
                    AND (mpr.created_at < ? AND (mpr.deleted_at IS NULL or mpr.deleted_at > ?))
                    AND extract(year from midata_person.birthday) = ?
                    AND midata_role.role_type IN (?);",
            [
                $endDate,
                $endDate,
                $groupIds,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes),
                $groupIds,
                $endDate,
                $endDate,
                $endDate,
                $year,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes)
            ],
            [
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll();
    }

    /**
     * @param string $date
     * @param array $groupIds
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findBirthYearsForDepartment(string $date, array $groupIds)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT extract(year from midata_person.birthday) as year
                    FROM midata_person_role 
                    INNER JOIN midata_role ON midata_person_role.role_id = midata_role.id 
                    INNER JOIN midata_person ON midata_person_role.person_id = midata_person.id
                    WHERE midata_person.birthday IS NOT NULL 
                        AND midata_person_role.group_id IN (?)  
                        AND (created_at < ? AND (deleted_at IS NULL or deleted_at > ?)) 
                        AND midata_role.role_type IN (?) ORDER BY year;",
            [
                $groupIds,
                $date,
                $date,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes)
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll(FetchMode::COLUMN);
    }

    // entered-left queries

    /**
     * @param array $groupIds
     * @param string $previousDate
     * @param string $currentDate
     * @return array|false|mixed
     * @throws DBALException
     */
    public function findAllNewPeopleByIdsAndGroup(array $groupIds, string $previousDate, string $currentDate)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT mpr.person_id, midata_person.gender as gender, (
                        SELECT DISTINCT count(midata_person_role.id) FROM midata_person_role
                            INNER JOIN midata_role mr on midata_person_role.role_id = mr.id
                                WHERE midata_person_role.person_id = mpr.person_id
                                    AND midata_person_role.created_at < ?
                                    AND midata_person_role.group_id IN (?)
                                    AND (midata_person_role.deleted_at IS NULL OR midata_person_role.deleted_at >= ?)
                                    AND mr.role_type IN (?)
                    ) as previous_active_role_count 
                FROM midata_person_role as mpr
                INNER JOIN midata_role ON mpr.role_id = midata_role.id
                INNER JOIN midata_person ON mpr.person_id = midata_person.id
                WHERE mpr.group_id IN (?)
                    AND mpr.created_at >= ?
                    AND mpr.created_at < ?
                    AND midata_person.leaving_date IS NULL
                    AND midata_role.role_type IN (?);",
            [
                $previousDate,
                $groupIds,
                $previousDate,
                array_merge(WidgetAggregator::$memberRoleTypes, WidgetAggregator::$leadersRoleTypes),
                $groupIds,
                $previousDate,
                $currentDate,
                array_merge(WidgetAggregator::$memberRoleTypes, WidgetAggregator::$leadersRoleTypes),
            ],
            [
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll();
    }

    /**
     * @param array $groupIds
     * @param string $previousDate
     * @param string $currentDate
     * @return array|mixed[]
     * @throws DBALException
     */
    public function findAllLeftPeopleByIdsAndGroup(array $groupIds, string $previousDate, string $currentDate)
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT mpr.person_id, midata_person.gender as gender, (
                        SELECT DISTINCT count(midata_person_role.id) FROM midata_person_role
                        INNER JOIN midata_role mr on midata_person_role.role_id = mr.id
                        WHERE midata_person_role.person_id = mpr.person_id
                          AND midata_person_role.created_at <= ?
                          AND (midata_person_role.deleted_at IS NULL OR midata_person_role.deleted_at >= ?)
                          AND midata_person_role.group_id IN (?)
                          AND mr.role_type IN (?)
                    ) as active_roles_in_group
            FROM midata_person_role as mpr
            INNER JOIN midata_role ON mpr.role_id = midata_role.id
            INNER JOIN midata_person ON mpr.person_id = midata_person.id
                WHERE mpr.group_id IN (?)
                    AND mpr.deleted_at >= ?
                    AND mpr.deleted_at < ?
                    AND mpr.created_at < ?
                    AND midata_role.role_type IN (?);",
            [
                $currentDate,
                $currentDate,
                $groupIds,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes),
                $groupIds,
                $previousDate,
                $currentDate,
                $previousDate,
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes),
            ],
            [
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
                Connection::PARAM_STR_ARRAY
            ]
        );
        return $statement->fetchAll();
    }

    public function findPersonRoleInPeriod(
        string $startDate,
        string $endDate,
        array $groupIds,
        int $personId
    ) {
        return $this->createQueryBuilder('personRole')
            ->innerJoin('personRole.person', 'person')
            ->innerJoin('personRole.group', 'g')
            ->innerJoin('personRole.role', 'role')
            ->where('person.id = :id')
            ->andWhere('g.id IN (:groupIds)')
            ->andWhere('role.roleType IN (:roles)')
            ->andWhere('personRole.createdAt >= :startDate')
            ->andWhere('personRole.createdAt < :endDate')
            ->andWhere('personRole.deletedAt IS NULL OR personRole.deletedAt > :endDate')
            ->setParameter('id', $personId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('groupIds', $groupIds)
            ->setParameter(
                'roles',
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes)
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param array $groupIds
     * @param int $personId
     * @return int|mixed|string
     */
    public function findAllDeletedForGroupPersonIdAndPeriod(
        string $startDate,
        string $endDate,
        array $groupIds,
        int $personId
    ) {
        return $this->createQueryBuilder('personRole')
            ->innerJoin('personRole.person', 'person')
            ->innerJoin('personRole.group', 'g')
            ->innerJoin('personRole.role', 'role')
            ->where('person.id = :id')
            ->andWhere('g.id IN (:groupIds)')
            ->andWhere('role.roleType IN (:roles)')
            ->andWhere('personRole.createdAt < :startDate')
            ->andWhere('personRole.deletedAt IS NOT NULL')
            ->andWhere('personRole.deletedAt >= :startDate')
            ->andWhere('personRole.deletedAt < :endDate')
            ->setParameter('id', $personId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('groupIds', $groupIds)
            ->setParameter(
                'roles',
                array_merge(WidgetAggregator::$leadersRoleTypes, WidgetAggregator::$memberRoleTypes)
            )
            ->getQuery()
            ->getResult();
    }

    public function findAllPersonInGroupByRole(array $groupTypes, array $roleTypes): array
    {
        $conn = $this->_em->getConnection();
        $statement = $conn->executeQuery(
            "SELECT DISTINCT midata_person_role.person_id, midata_person_role.group_id
                FROM midata_person_role
                JOIN midata_role ON midata_person_role.role_id = midata_role.id
                JOIN midata_group ON midata_person_role.group_id = midata_group.id
                JOIN midata_group_type ON midata_group.group_type_id = midata_group_type.id
                WHERE midata_group_type.group_type IN (?)
                    AND midata_role.role_type IN (?)
                    AND midata_person_role.deleted_at IS NULL;",
            [
                $groupTypes,
                $roleTypes,
            ],
            [
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_STR_ARRAY,
            ]
        );
        return $statement->fetchAll();
    }
}

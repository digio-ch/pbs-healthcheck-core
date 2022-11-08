<?php

namespace App\Service;

use App\Entity\Group;
use App\Entity\GroupType;
use App\Entity\QualificationType;
use App\Entity\Question;
use App\Entity\Role;
use App\Repository\GroupRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

class QuapComputeAnswersService
{
    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    public function __construct(
        GroupRepository $groupRepository,
        EntityManagerInterface $em
    ) {
        $this->groupRepository = $groupRepository;
        $this->em = $em;
    }

    public function computeAnswer(string $evaluationFunction, Group $group): int
    {
        switch ($evaluationFunction) {
            case 'has_percentage_woelf':
                return $this->hasPercentageWoelf($group);
            case 'has_percentage_pfadi':
                return $this->hasPercentagePfadi($group);
            case 'has_percentage_biber':
                return $this->hasPercentageBiber($group);
            case 'has_percentage_pio':
                return $this->hasPercentagePio($group);
            case 'group_leaders_educated':
                return $this->groupLeadersEducated($group);
            case 'group_president_educated':
                return $this->groupPresidentsEducated($group);
            case 'has_coach':
                return $this->hasCoach($group);
            case 'has_parents_council':
                return $this->hasParentsCouncil($group);
            case 'age_leitpfadi':
                return $this->ageLeitpfadi($group);
            case 'has_biber':
                return $this->hasBiber($group);
            case 'has_woelf':
                return $this->hasWoelf($group);
            case 'has_pfadi':
                return $this->hasPfadi($group);
            case 'has_pio':
                return $this->hasPio($group);
            case 'has_rover':
                return $this->hasRover($group);
            case 'all_leaders_member':
                return $this->allLeadersMember($group);
            case 'leader_age_woelf_pfadi':
                return $this->leaderAgeWoelfPfadi($group);
            case 'leader_age_pio':
                return $this->leaderAgePio($group);
            case 'leader_age_biber':
                return $this->leaderAgeBiber($group);
            case 'no_double_roles':
                return $this->noDoubleRoles($group);
            case 'leader_biber':
                return $this->leaderBiber($group);
            case 'leader_woelf':
                return $this->leaderWoelf($group);
            case 'leader_pfadi':
                return $this->leaderPfadi($group);
            case 'leader_pio':
                return $this->leaderPio($group);
            case 'leader_rover':
                return $this->leaderRover($group);
            case 'leader_pta':
                return $this->leaderPta($group);
            case 'has_leitpfadi':
                return $this->hasLeitpfadi($group);
            case 'split_lead_coach':
                return $this->splitLeadCoach($group);
            case 'has_leader':
                return $this->hasLeader($group);
            case 'has_president':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PRESIDENT);
            case 'has_biber_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_BIBERSTUFE_V);
            case 'has_wolf_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_WOLFSTUFE_V);
            case 'has_pfadi_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PFADISTUFE_V);
            case 'has_pio_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PIOSTUFE_V);
            case 'has_rover_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_ROVERSTUFE_V);
            case 'has_pta_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PFADI_TROTZ_ALLEM_V);
            case 'has_education_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_AUSBILDUNG_V);
            case 'has_coach_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_BETREUUNG_V);
            case 'has_diversity_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_INTEGRATION_V);
            case 'has_international_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_INTERNATIONALES_V);
            case 'has_crisis_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_KRISENTEAM_V);
            case 'has_pr_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PR_V);
            case 'has_prevention_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PRAEVENTION_SEXUELLER_AUSBEUTNG_V);
            case 'has_program_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_PROGRAMM_V);
            case 'has_sustainability_v':
                return $this->hasRoleWrapper($group, Role::CANTONAL_NACHHALTIGKEIT_V);
            default:
                return Question::ANSWER_NOT_ANSWERED;
        }
    }

    private function hasPercentageWoelf(Group $group): int
    {
        return $this->hasPercentage(
            $this->getGroupIds($group),
            ROLE::LEADER_ROLES_WOELFE,
            [
                QualificationType::JS_LEITER_KINDERSPORT,
            ]
        );
    }

    private function hasPercentagePfadi(Group $group): int
    {
        return $this->hasPercentage(
            $this->getGroupIds($group),
            ROLE::LEADER_ROLES_PFADI,
            [
                QualificationType::JS_LEITER_JUGENDSPORT,
            ]
        );
    }

    private function hasPercentageBiber(Group $group): int
    {
        return $this->hasPercentageComplex(
            $this->getGroupIds($group),
            ROLE::LEADER_ROLES_BIBER,
            [
                QualificationType::ABSOLVENT_EINFUEHRUNGSKURS_BIBER,
            ],
            [
                QualificationType::JS_LEITER_KINDERSPORT,
                QualificationType::JS_LEITER_JUGENDSPORT,
            ]
        );
    }

    private function hasPercentagePio(Group $group): int
    {
        return $this->hasPercentageComplex(
            $this->getGroupIds($group),
            ROLE::LEADER_ROLES_PIO,
            [
                QualificationType::ABSOLVENT_EINFUEHRUNGSKURS_PIO,
            ],
            [
                QualificationType::JS_LEITER_KINDERSPORT,
                QualificationType::JS_LEITER_JUGENDSPORT,
            ]
        );
    }

    private function hasPercentage(array $groupIds, array $leaderRoles, array $qualificationIds): int
    {
        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT (CASE
                    WHEN count_member >= 1 THEN ((100::float / count_member * count_recognition) >= 66)
                    ELSE FALSE
                END) AS result FROM (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_member FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                ) AS count_member, (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_recognition FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    JOIN midata_person_qualification ON midata_person_role.person_id = midata_person_qualification.person_id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                    AND midata_person_qualification.qualification_type_id IN (?)
                ) AS count_recognition;
            ",
            [
                $groupIds,
                $leaderRoles,
                $groupIds,
                $leaderRoles,
                $qualificationIds
            ],
            [
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function hasPercentageComplex(array $groupIds, array $leaderRoles, array $mainQualificationIds, array $additionalQualificationIds): int
    {
        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT (CASE
                    WHEN count_member >= 1 THEN ((100::float / count_member * count_recognition) >= 66)
                    ELSE FALSE
                END) AS result FROM (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_member FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                ) AS count_member, (
                SELECT count(id_1.id) AS count_recognition FROM (
                    SELECT DISTINCT midata_person_role.person_id AS id FROM midata_person_role
                        JOIN midata_role ON midata_person_role.role_id = midata_role.id
                        JOIN midata_person_qualification ON midata_person_role.person_id = midata_person_qualification.person_id
                        WHERE midata_person_role.group_id IN (?)
                        AND midata_role.role_type IN (?)
                        AND midata_person_qualification.qualification_type_id IN (?)) AS id_1
                    JOIN (
                        SELECT DISTINCT midata_person_role.person_id AS id FROM midata_person_role
                            JOIN midata_role ON midata_person_role.role_id = midata_role.id
                            JOIN midata_person_qualification ON midata_person_role.person_id = midata_person_qualification.person_id
                            WHERE midata_person_role.group_id IN (?)
                            AND midata_role.role_type IN (?)
                            AND midata_person_qualification.qualification_type_id IN (?)) AS id_2
                    ON id_1.id = id_2.id
                ) AS count_recognition;
            ",
            [
                $groupIds,
                $leaderRoles,
                $groupIds,
                $leaderRoles,
                $mainQualificationIds,
                $groupIds,
                $leaderRoles,
                $additionalQualificationIds
            ],
            [
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function groupLeadersEducated(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT id_leader = id_educated AS result FROM (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS id_leader FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                ) AS id_leader, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS id_educated FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    JOIN midata_person_qualification ON midata_person_role.person_id = midata_person_qualification.person_id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                    AND midata_person_qualification.qualification_type_id = ?
                ) AS id_educated;
            ",
            [
                $groupIds,
                Role::DEPARTMENT_LEADER_ROLES,
                $groupIds,
                Role::DEPARTMENT_LEADER_ROLES,
                QualificationType::JS_LAGERLEITER,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::INTEGER,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function groupPresidentsEducated(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT (id_president = id_educated_1) AND (id_president = id_educated_2) AS result FROM (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS id_president FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                ) AS id_president, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS id_educated_1 FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    JOIN midata_person_qualification ON midata_person_role.person_id = midata_person_qualification.person_id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                    AND midata_person_qualification.qualification_type_id = ?
                ) AS id_educated_1, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS id_educated_2 FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    JOIN midata_person_qualification ON midata_person_role.person_id = midata_person_qualification.person_id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                    AND midata_person_qualification.qualification_type_id = ?
                ) AS id_educated_2;
            ",
            [
                $groupIds,
                Role::DEPARTMENT_PRESIDENT_ROLES,
                $groupIds,
                Role::DEPARTMENT_PRESIDENT_ROLES,
                QualificationType::ABSOLVENT_AL,
                $groupIds,
                Role::DEPARTMENT_PRESIDENT_ROLES,
                QualificationType::ABSOLVENT_PANORAMAKURS,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::INTEGER,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                ParameterType::INTEGER,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function hasCoach(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_COACH);
    }

    private function hasParentsCouncil(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT count_parents_council >= 1 AND count_president_parents_council >= 1 FROM (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_parents_council FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS count_parents_council, (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_president_parents_council FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS count_president_parents_council
            ",
            [
                $groupIds,
                Role::PARENTS_COUNCIL_MEMBER,
                $groupIds,
                Role::PARENTS_COUNCIL_PRESIDENT,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function ageLeitpfadi(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        // 05. 05. 2021 // example start date
        // 06. 05. 2006 // 14 yo, 15 tomorrow // today - 15y + 1d
        // 05. 05. 2008 // 13 yo, 13 today    // today - 13y
        $now = new \DateTime('now');

        $minDuration = new \DateInterval('P15Y');
        $minDuration->invert = 1;
        $oneDay = new \DateInterval('P1D');
        $minDate = $now->add($minDuration)->add($oneDay);

        $maxDuration = new \DateInterval('P13Y');
        $maxDuration->invert = 1;
        $maxDate = $now->add($maxDuration);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT count_leitpfadi = count_count_leitpfadi_age AND count_leitpfadi >= 1 FROM (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_leitpfadi FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS count_leitpfadi, (
                SELECT count(DISTINCT midata_person_role.person_id) AS count_count_leitpfadi_age FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    JOIN midata_person ON midata_person_role.person_id = midata_person.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                    AND midata_person.birthday > ?::date AND midata_person.birthday < ?::date
                ) AS count_count_leitpfadi_age
            ",
            [
                $groupIds,
                Role::PFADI_LEITPFADI,
                $groupIds,
                Role::PFADI_LEITPFADI,
                $minDate->format('Y-m-d'),
                $maxDate->format('Y-m-d'),
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                ParameterType::STRING,
                ParameterType::STRING,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function hasBiber(Group $group): int
    {
        return $this->hasGroup(
            $this->getGroupIds($group),
            GroupType::BIBER
        );
    }

    private function hasWoelf(Group $group): int
    {
        return $this->hasGroup(
            $this->getGroupIds($group),
            GroupType::WOELFE
        );
    }

    private function hasPfadi(Group $group): int
    {
        return $this->hasGroup(
            $this->getGroupIds($group),
            GroupType::PFADI
        );
    }

    private function hasPio(Group $group): int
    {
        return $this->hasGroup(
            $this->getGroupIds($group),
            GroupType::PIO
        );
    }

    private function hasRover(Group $group): int
    {
        return $this->hasGroup(
            $this->getGroupIds($group),
            GroupType::ROVER
        );
    }

    private function hasGroup(array $groupIds, int $groupTypeId): int
    {
        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT count(DISTINCT id) > 0 FROM midata_group
                WHERE midata_group.id IN (?)
                AND midata_group.group_type_id = ?
            ",
            [
                $groupIds,
                $groupTypeId,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::INTEGER,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function allLeadersMember(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT ids_leader @> array_cat(ids_rover_leader, ids_rover) FROM (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_leader FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                ) AS ids_leader, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_rover_leader FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_rover_leader, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_rover FROM midata_person_role
                    JOIN midata_group ON midata_person_role.group_id = midata_group.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_group.group_type_id = ?
                ) AS ids_rover;
            ",
            [
                $groupIds,
                Role::LEADER_ROLES,
                $groupIds,
                Role::DEPARTMENT_LEADER_ROVER,
                $groupIds,
                GroupType::ROVER,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::INTEGER,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function leaderAgeWoelfPfadi(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $now = new \DateTime('now');

        $maxDuration = new \DateInterval('P17Y');
        $maxDuration->invert = 1;
        $maxDate = $now->add($maxDuration);

        return $this->leaderAge($groupIds, [
            ...Role::LEADER_ROLES_WOELFE,
            ...Role::LEADER_ROLES_PFADI
        ], $maxDate->format('Y-m-d'));
    }

    private function leaderAgePio(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $now = new \DateTime('now');

        $maxDuration = new \DateInterval('P19Y');
        $maxDuration->invert = 1;
        $maxDate = $now->add($maxDuration);

        return $this->leaderAge($groupIds, Role::LEADER_ROLES_PIO, $maxDate->format('Y-m-d'));
    }

    private function leaderAgeBiber(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $now = new \DateTime('now');

        $maxDuration = new \DateInterval('P20Y');
        $maxDuration->invert = 1;
        $maxDate = $now->add($maxDuration);

        return $this->leaderAge($groupIds, Role::LEADER_ROLES_BIBER, $maxDate->format('Y-m-d'));
    }

    private function leaderAge(array $groupIds, array $leaderRoles, string $date): int
    {
        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT bool_and(birthday < ?::date) FROM midata_person
                WHERE midata_person.id IN (
                    SELECT DISTINCT midata_person_role.person_id FROM midata_person_role
                        JOIN midata_role ON midata_person_role.role_id = midata_role.id
                        WHERE midata_person_role.group_id IN (?)
                        AND midata_role.role_type IN (?)
                )
            ",
            [
                $date,
                $groupIds,
                $leaderRoles,
            ],
            [
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function noDoubleRoles(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT 
                (NOT (ids_biber && ids_woelfe) AND NOT (ids_biber && ids_pfadi) AND NOT (ids_biber && ids_pio) AND NOT (ids_biber && ids_rover) AND NOT (ids_biber && ids_pta)) AND
                (NOT (ids_woelfe && ids_pfadi) AND NOT (ids_woelfe && ids_pio) AND NOT (ids_woelfe && ids_rover) AND NOT (ids_woelfe && ids_pta)) AND
                (NOT (ids_pfadi && ids_pio) AND NOT (ids_pfadi && ids_rover) AND NOT (ids_pfadi && ids_pta)) AND
                (NOT (ids_pio && ids_rover) AND NOT (ids_pio && ids_pta)) AND
                (NOT (ids_rover && ids_pta))
                FROM (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_biber FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_biber, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_woelfe FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_woelfe, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_pfadi FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_pfadi, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_pio FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_pio, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_rover FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_rover, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_pta FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_pta;
            ",
            [
                $groupIds,
                Role::DEPARTMENT_LEADER_BIBER,
                $groupIds,
                Role::DEPARTMENT_LEADER_WOELFE,
                $groupIds,
                Role::DEPARTMENT_LEADER_PFADI,
                $groupIds,
                Role::DEPARTMENT_LEADER_PIO,
                $groupIds,
                Role::DEPARTMENT_LEADER_ROVER,
                $groupIds,
                Role::DEPARTMENT_LEADER_PTA,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function leaderBiber(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_LEADER_BIBER);
    }

    private function leaderWoelf(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_LEADER_WOELFE);
    }

    private function leaderPfadi(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_LEADER_PFADI);
    }

    private function leaderPio(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_LEADER_PIO);
    }

    private function leaderRover(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_LEADER_ROVER);
    }

    private function leaderPta(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::DEPARTMENT_LEADER_PTA);
    }

    private function hasLeitpfadi(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        return $this->hasRole($groupIds, Role::PFADI_LEITPFADI);
    }

    private function hasLeader(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);
        //$role = $group->getGroupType()->getId() == GroupType::CANTON ? :'b';
        return $this->hasNumRole($groupIds, Role::CANTONAL_LEADER, 2);
    }

    private function hasRoleWrapper(Group $group, string $role): int
    {
        $groupIds = $this->getGroupIds($group);
        return $this->hasRole($groupIds, $role);
    }

    private function hasRole(array $groupIds, string $role): int
    {
        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT count(DISTINCT midata_person_role.person_id) >= 1 FROM midata_person_role
                JOIN midata_role ON midata_person_role.role_id = midata_role.id
                WHERE midata_person_role.group_id IN (?)
                AND midata_role.role_type = ?
            ",
            [
                $groupIds,
                $role,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function hasNumRole(array $groupIds, string $role, int $count): int
    {
        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT count(DISTINCT midata_person_role.person_id) FROM midata_person_role
                JOIN midata_role ON midata_person_role.role_id = midata_role.id
                WHERE midata_person_role.group_id IN (?)
                AND midata_role.role_type = ?
            ",
            [
                $groupIds,
                $role,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
            ]
        )->fetchOne();

        return $result >= $count ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }

    private function splitLeadCoach(Group $group): int
    {
        $groupIds = $this->getGroupIds($group);

        $result = $this->em->getConnection()->executeQuery(
            "
            SELECT (NOT (ids_coach && ids_leader)) FROM (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_coach FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type = ?
                ) AS ids_coach, (
                SELECT array_agg(DISTINCT midata_person_role.person_id) AS ids_leader FROM midata_person_role
                    JOIN midata_role ON midata_person_role.role_id = midata_role.id
                    WHERE midata_person_role.group_id IN (?)
                    AND midata_role.role_type IN (?)
                ) AS ids_leader;
            ",
            [
                $groupIds,
                Role::DEPARTMENT_COACH,
                $groupIds,
                Role::LEADER_ROLES,
            ],
            [
                Connection::PARAM_INT_ARRAY,
                ParameterType::STRING,
                Connection::PARAM_INT_ARRAY,
                Connection::PARAM_STR_ARRAY,
            ]
        )->fetchOne();

        return $result ? Question::ANSWER_FULLY_APPLIES : Question::ANSWER_DONT_APPLIES;
    }


    private function getGroupIds(Group $group): array
    {
        $subGroupIds = $this->groupRepository->findAllSubGroupIdsByParentGroupId($group->getId());
        return array_merge($subGroupIds, [$group->getId()]);
    }
}

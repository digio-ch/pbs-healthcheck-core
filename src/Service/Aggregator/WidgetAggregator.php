<?php

namespace App\Service\Aggregator;

use App\Entity\Midata\PersonRole;
use App\Repository\Aggregated\AggregatedEntityRepository;
use App\Repository\Midata\GroupRepository;
use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

abstract class WidgetAggregator
{
    public const AGGREGATION_START_DATE = '2014-01-01';

    public static $memberRoleTypes = [
        'Group::Pta::Mitglied',
        'Group::AbteilungsRover::Rover',
        'Group::Pio::Pio',
        'Group::Pfadi::Pfadi',
        'Group::Pfadi::Leitpfadi',
        'Group::Woelfe::Wolf',
        'Group::Woelfe::Leitwolf',
        'Group::Biber::Biber'
    ];

    public static $leaderRoleTypesByGroupType = [
        'Group::Abteilung' => [
            'Group::Abteilung::Abteilungsleitung',
            'Group::Abteilung::AbteilungsleitungStv'
        ],
        'Group::Pta' => [
            'Group::Abteilung::StufenleitungPta',
            'Group::Pta::Einheitsleitung',
            'Group::Pta::Mitleitung'
        ],
        'Group::AbteilungsRover' => [
            'Group::Abteilung::StufenleitungRover',
            'Group::AbteilungsRover::Einheitsleitung',
            'Group::AbteilungsRover::Mitleitung'
        ],
        'Group::Pio' => [
            'Group::Abteilung::StufenleitungPio',
            'Group::Pio::Einheitsleitung',
            'Group::Pio::Mitleitung'
        ],
        'Group::Pfadi' => [
            'Group::Abteilung::StufenleitungPfadi',
            'Group::Pfadi::Einheitsleitung',
            'Group::Pfadi::Mitleitung'
        ],
        'Group::Woelfe' => [
            'Group::Abteilung::StufenleitungWoelfe',
            'Group::Woelfe::Einheitsleitung',
            'Group::Woelfe::Mitleitung'
        ],
        'Group::Biber' => [
            'Group::Abteilung::StufenleitungBiber',
            'Group::Biber::Einheitsleitung',
            'Group::Biber::Mitleitung'
        ],
    ];

    public static $groupTypeByLeaderRoleType = [
        'Group::Pta::Mitglied' => 'Group::Pta',
        'Group::Abteilung::StufenleitungPta' => 'Group::Pta',
        'Group::Pta::Einheitsleitung' => 'Group::Pta',
        'Group::Pta::Mitleitung' => 'Group::Pta',

        'Group::AbteilungsRover::Rover' => 'Group::AbteilungsRover',
        'Group::Abteilung::StufenleitungRover' => 'Group::AbteilungsRover',
        'Group::AbteilungsRover::Einheitsleitung' => 'Group::AbteilungsRover',
        'Group::AbteilungsRover::Mitleitung' => 'Group::AbteilungsRover',

        'Group::Pio::Pio' => 'Group::Pio',
        'Group::Abteilung::StufenleitungPio' => 'Group::Pio',
        'Group::Pio::Einheitsleitung' => 'Group::Pio',
        'Group::Pio::Mitleitung' => 'Group::Pio',

        'Group::Pfadi::Pfadi' => 'Group::Pfadi',
        'Group::Pfadi::Leitpfadi' => 'Group::Pfadi',
        'Group::Abteilung::StufenleitungPfadi' => 'Group::Pfadi',
        'Group::Pfadi::Einheitsleitung' => 'Group::Pfadi',
        'Group::Pfadi::Mitleitung' => 'Group::Pfadi',

        'Group::Woelfe::Wolf' => 'Group::Woelfe',
        'Group::Woelfe::Leitwolf' => 'Group::Woelfe',
        'Group::Abteilung::StufenleitungWoelfe' => 'Group::Woelfe',
        'Group::Woelfe::Einheitsleitung' => 'Group::Woelfe',
        'Group::Woelfe::Mitleitung' => 'Group::Woelfe',

        'Group::Biber::Biber' => 'Group::Biber',
        'Group::Abteilung::StufenleitungBiber' => 'Group::Biber',
        'Group::Biber::Einheitsleitung' => 'Group::Biber',
        'Group::Biber::Mitleitung' => 'Group::Biber',
    ];

    public static $leadersRoleTypes = [
        'Group::Abteilung::StufenleitungPta',
        'Group::Abteilung::StufenleitungRover',
        'Group::Abteilung::StufenleitungPio',
        'Group::Abteilung::StufenleitungPfadi',
        'Group::Abteilung::StufenleitungWoelfe',
        'Group::Abteilung::StufenleitungBiber',
        'Group::Pta::Einheitsleitung',
        'Group::Pta::Mitleitung',
        'Group::AbteilungsRover::Einheitsleitung',
        'Group::AbteilungsRover::Mitleitung',
        'Group::Pio::Einheitsleitung',
        'Group::Pio::Mitleitung',
        'Group::Pfadi::Einheitsleitung',
        'Group::Pfadi::Mitleitung',
        'Group::Woelfe::Einheitsleitung',
        'Group::Woelfe::Mitleitung',
        'Group::Biber::Einheitsleitung',
        'Group::Biber::Mitleitung',
    ];

    public static $mainGroupRoleTypes = [
        'Group::Abteilung::Abteilungsleitung',
        'Group::Abteilung::AbteilungsleitungStv',
    ];

    public static $typePriority = [
        'Group::Abteilung',
        'Group::Pta',
        'Group::AbteilungsRover',
        'Group::Pio',
        'Group::Pfadi',
        'Group::Woelfe',
        'Group::Biber',
    ];

    public static $roleTypePriority = [
        'Group::Abteilung::StufenleitungPta',
        'Group::Pta::Einheitsleitung',
        'Group::Pta::Mitleitung',

        'Group::AbteilungsRover::Rover',
        'Group::Abteilung::StufenleitungRover',
        'Group::AbteilungsRover::Einheitsleitung',
        'Group::AbteilungsRover::Mitleitung',

        'Group::Abteilung::StufenleitungPio',
        'Group::Pio::Einheitsleitung',
        'Group::Pio::Mitleitung',

        'Group::Pfadi::Leitpfadi',
        'Group::Abteilung::StufenleitungPfadi',
        'Group::Pfadi::Einheitsleitung',
        'Group::Pfadi::Mitleitung',

        'Group::Woelfe::Leitwolf',
        'Group::Abteilung::StufenleitungWoelfe',
        'Group::Woelfe::Einheitsleitung',
        'Group::Woelfe::Mitleitung',

        'Group::Abteilung::StufenleitungBiber',
        'Group::Biber::Einheitsleitung',
        'Group::Biber::Mitleitung',

        'Group::Pta::Mitglied',

        'Group::Pio::Pio',

        'Group::Pfadi::Pfadi',

        'Group::Woelfe::Wolf',

        'Group::Biber::Biber',
    ];

    public static $typeOrder = [
        'Group::Biber',
        'Group::Woelfe',
        'Group::Pfadi',
        'Group::Pio',
        'Group::Pta',
        'Group::AbteilungsRover',
        'Group::Abteilung'
    ];

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * WidgetAggregator constructor.
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    abstract public function getName();

    abstract public function aggregate(DateTime $startDate = null);

    public function groupSubGroupsByGroupType(array $subGroups)
    {
        $groupsByType = [];
        foreach ($subGroups as $subGroup) {
            if (!array_key_exists($subGroup['group_type_id'], $groupsByType)) {
                $groupsByType[$subGroup['group_type_id']] = [
                    'group_type' => $subGroup['group_type'],
                    'groups' => []
                ];
            }
            array_push($groupsByType[$subGroup['group_type_id']]['groups'], $subGroup['id']);
        }
        return $groupsByType;
    }

    /**
     * @param PersonRole $personRole
     * @return string
     */
    public function findLeaderGroupTypeForRoleType(PersonRole $personRole): string
    {
        foreach (self::$leaderRoleTypesByGroupType as $groupType => $roleTypes) {
            foreach ($roleTypes as $roleType) {
                if ($roleType === $personRole->getRole()->getRoleType()) {
                    return $groupType;
                }
            }
        }
        return $personRole->getGroup()->getGroupType()->getGroupType();
    }

    /**
     * @param array|PersonRole[] $personRoles
     * @return array|null
     */
    public function findGroupAndPersonTypeByPersonRolesHierarchy(array $personRoles)
    {
        foreach (self::$leadersRoleTypes as $leaderRole) {
            /** @var PersonRole $personRole */
            foreach ($personRoles as $personRole) {
                if (trim($personRole->getRole()->getRoleType()) !== $leaderRole) {
                    continue;
                }
                return ['leaders', $this->findLeaderGroupTypeForRoleType($personRole)];
            }
        }
        foreach (self::$memberRoleTypes as $memberRole) {
            /** @var PersonRole $personRole */
            foreach ($personRoles as $personRole) {
                if (trim($personRole->getRole()->getRoleType()) !== $memberRole) {
                    continue;
                }
                return ['members', $personRole->getGroup()->getGroupType()->getGroupType()];
            }
        }
        return null;
    }

    /**
     * Expects idsAndRoles in the following structure:
     * [
     *      [
     *          'person_id' => X,
     *          'gender' => X,
     *      ], ...
     * ]
     * The returned array will be structured in the following way:
     * [
     *      "group_type" => [
     *          "person_type" => ["gender" => X, ...],
     *          ...
     *      ],
     *      ...
     * ]
     *
     * @param array $idsAndRoles
     * @param array $allGroupIds
     * @param string $endDate
     * @return array[]
     */
    protected function processPersonIdsAndRoles(
        array $idsAndRoles,
        array $allGroupIds,
        string $endDate
    ) {
        $countByGroupType = [];
        foreach ($idsAndRoles as $personIdRoleCountAndGender) {
            /** @var PersonRole[]|null $personRoles */
            $personRoles = $this->personRoleRepository->findPersonRoles(
                $personIdRoleCountAndGender['person_id'],
                $endDate,
                $allGroupIds
            );
            if (!$personRoles) {
                continue;
            }
            $groupTypeAndPersonType = $this->findGroupAndPersonTypeByPersonRolesHierarchy($personRoles);
            if ($groupTypeAndPersonType === null) {
                continue;
            }

            $gender = empty($personIdRoleCountAndGender['gender']) ? 'u' : $personIdRoleCountAndGender['gender'];
            $personType = $groupTypeAndPersonType[0];
            $groupType = $groupTypeAndPersonType[1];

            if (!array_key_exists($groupType, $countByGroupType)) {
                $countByGroupType[$groupType] = [
                    'members' => ['m' => 0, 'w' => 0, 'u' => 0],
                    'leaders' => ['m' => 0, 'w' => 0, 'u' => 0]
                ];
            }
            $countByGroupType[$groupType][$personType][$gender] += 1;
        }
        return $countByGroupType;
    }

    /**
     * @param string $date
     * @param array $data
     * @return bool
     */
    protected function isDataExistsForDate(string $date, array $data): bool
    {
        return in_array($date, $data);
    }

    /**
     * @param AggregatedEntityRepository $repository
     * @param int $mainGroupId
     * @return array
     * @throws DBALException
     */
    protected function getAllDataPointDates(AggregatedEntityRepository $repository, int $mainGroupId): array
    {
        return $repository->getAllDataPointDates($mainGroupId);
    }

    /**
     * @param AggregatedEntityRepository $repository
     * @param int $mainGroupId
     * @throws ORMException
     */
    protected function deleteLastPeriod(AggregatedEntityRepository $repository, int $mainGroupId): void
    {
        $to = new DateTime();
        $from = clone $to;
        $from->modify('first day of this month');

        // If it's the first day delete last day of last month
        if ($to->format('j') == 1) {
            $to->modify('last day of last month');
            $to->setTime(23,59, 59, 999);
            $from->modify('last day of last month');
            $from->setTime(0,0);
        }



        $data = $repository->createQueryBuilder('w')
            ->join('w.group', 'g')
            ->where('g.id = :groupId')
            ->andWhere('w.dataPointDate >= :from')
            ->andWhere('w.dataPointDate <= :to')
            ->setParameter('groupId', $mainGroupId)
            ->setParameter('from', $from->format('Y-m-d'))
            ->setParameter('to', $to->format('Y-m-d'))
            ->getQuery()
            ->getResult();

        if (!$data) {
            return;
        }

        foreach ($data as $item) {
            $repository->remove($item);
        }
        $repository->flush();
    }
}

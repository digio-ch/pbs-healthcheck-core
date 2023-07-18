<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Widgets\RoleOverview\RoleOccupation;
use App\DTO\Model\Apps\Widgets\RoleOverview\RoleOccupationWrapper;
use App\DTO\Model\Apps\Widgets\RoleOverview\RoleOverviewDTO;
use App\Entity\Aggregated\AggregatedPersonRole;
use App\Entity\General\GroupSettings;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Midata\Role;

class RoleOverviewMapper
{
    const GROUP_TYPE_COLORS = [
        'Biber' => ['#EEE09F', '#d6ca8f'],
        'Woelfe' => ['#3BB5DC', '#2f91b0'],
        'Pfadi' => ['#9A7A54', '#7b6243'],
        'Pio' => ['#DD1F19', '#b11914'],
        'Rover' => ['#1DA650', '#178540'],
        'Pta' => ['#d9b826', '#ae931e'],
    ];

    public static function createRoleOverviewDTO(Group $group): RoleOverviewDTO
    {
        $groupSettings = $group->getGroupSettings();
        $filter = $groupSettings->getRoleOverviewFilter();
        if (!$filter || !sizeof($filter)) {
            if ($group->getGroupType()->getGroupType() === GroupType::DEPARTMENT) {
                $filter = GroupSettings::DEFAULT_DEPARMENT_ROLES;
            } elseif ($group->getGroupType()->getGroupType() === GroupType::REGION) {
                $filter = GroupSettings::DEFAULT_REGION_ROLES;
            } elseif ($group->getGroupType()->getGroupType() === GroupType::CANTON) {
                $filter = GroupSettings::DEFAULT_CANTONAL_ROLES;
            }
        }

        return new RoleOverviewDTO($filter);
    }

    public static function createRoleOccupationWrapper(Role $role, string $locale): RoleOccupationWrapper
    {
        if (str_contains($locale, 'it')) {
            $roleName = $role->getItLabel();
        } elseif (str_contains($locale, 'fr')) {
            $roleName = $role->getFrLabel();
        } else {
            $roleName = $role->getDeLabel();
        }
        return new RoleOccupationWrapper($roleName, $role->getRoleType(), self::getRoleColor($role->getRoleType())); // Colors not yet implemented
    }

    private static function getRoleColor(string $roleType)
    {
        foreach (RoleOverviewMapper::GROUP_TYPE_COLORS as $key => $value) {
            if (str_contains($roleType, $key)) {
                return $value;
            }
        }
        return ['#da70d6', '#ae5aab'];
    }

    public static function createRoleOccupation(AggregatedPersonRole $aggregatedPersonRole, string $from, string $to): RoleOccupation
    {
        $start = new \DateTime($from) < $aggregatedPersonRole->getStartAt() ? $aggregatedPersonRole->getStartAt()->format('Y-m-d') : $from;
        $end = is_null($aggregatedPersonRole->getEndAt()) || new \DateTime($to) <= $aggregatedPersonRole->getEndAt() ? $to : $aggregatedPersonRole->getEndAt()->format('Y-m-d');
        return new RoleOccupation($aggregatedPersonRole->getNickname(), $start, $end);
    }
}

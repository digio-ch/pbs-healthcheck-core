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

    public const GROUP_TYPE_COLORS = [
        'Group::Biber' => '#EEE09F',
        'Group::Woelfe' => '#3BB5DC',
        'Group::Pfadi' => '#9A7A54',
        'Group::Pio' => '#DD1F19',
        'Group::AbteilungsRover' => '#1DA650',
        'Group::Pta' => '#d9b826',
        'Group::Abteilung' => '#929292',
        'leaders' => '#929292'
    ];

    public static function createRoleOverviewDTO(Group $group): RoleOverviewDTO
    {
        $groupSettings = $group->getGroupSettings();
        $filter = $groupSettings->getRoleOverviewFilter();
        if(!$filter || !sizeof($filter)) {
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
        return new RoleOccupationWrapper($roleName, $role->getRoleType(), ['#080', '#050']); // Colors not yet implemented
    }

    public static function createRoleOccupation(AggregatedPersonRole $aggregatedPersonRole, string $from, string $to): RoleOccupation
    {
        $start = new \DateTime($from) < $aggregatedPersonRole->getStartAt() ? $aggregatedPersonRole->getStartAt()->format('Y-m-d') : $from;
        $end = is_null($aggregatedPersonRole->getEndAt()) || new \DateTime($to) <= $aggregatedPersonRole->getEndAt() ? $to : $aggregatedPersonRole->getEndAt()->format('Y-m-d');
        return new RoleOccupation($aggregatedPersonRole->getNickname(), $start, $end);
    }
}

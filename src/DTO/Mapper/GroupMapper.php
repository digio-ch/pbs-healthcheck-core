<?php

namespace App\DTO\Mapper;

use App\DTO\Model\GroupDTO;
use App\DTO\Model\GroupTypeDTO;
use App\Entity\Group;
use App\Entity\GroupType;
use App\Service\DataProvider\WidgetDataProvider;

class GroupMapper
{
    public static function createFromEntity(Group $group, string $locale): GroupDTO
    {
        $groupDTO = new GroupDTO();
        $groupDTO->setId($group->getId());
        $groupDTO->setName($group->getName());
        $groupDTO->setCantonName($group->getCantonName());
        $groupDTO->setCreatedAt($group->getCreatedAt()->format('Y-m-d'));
        $groupDTO->setDeletedAt($group->getDeletedAt() ? $group->getDeletedAt()->format('Y-m-d') : null);
        $groupDTO->setGroupType(GroupTypeMapper::createGroupTypeFromEntity($group->getGroupType(), $locale));
        return $groupDTO;
    }

    public static function createFromMidataOauthProfile(array $group, GroupType $groupType, string $locale): GroupDTO
    {
        $groupDTO = new GroupDTO();
        $groupDTO->setId($group['group_id']);
        $groupDTO->setName($group['group_name']);
        $groupDTO->setCantonName('' /* TODO really needed? */ );
        $groupDTO->setCreatedAt('' /* TODO really needed? */);
        $groupDTO->setDeletedAt('' /* TODO really needed? */);
        $groupDTO->setGroupType(GroupTypeMapper::createGroupTypeFromEntity($groupType, $locale));
        return $groupDTO;
    }
}

<?php

namespace App\DTO\Mapper;

use App\DTO\Model\GroupDTO;
use App\Entity\midata\Group;

class GroupMapper
{
    public static function createFromEntity(Group $group, string $locale, string $permissionType): GroupDTO
    {
        $groupDTO = new GroupDTO();
        $groupDTO->setId($group->getId());
        $groupDTO->setName($group->getName());
        $groupDTO->setCantonName($group->getCantonName());
        $groupDTO->setCreatedAt($group->getCreatedAt()->format('Y-m-d'));
        $groupDTO->setDeletedAt($group->getDeletedAt() ? $group->getDeletedAt()->format('Y-m-d') : null);
        $groupDTO->setGroupType(GroupTypeMapper::createGroupTypeFromEntity($group->getGroupType(), $locale));
        $groupDTO->setPermissionType($permissionType);
        return $groupDTO;
    }
}

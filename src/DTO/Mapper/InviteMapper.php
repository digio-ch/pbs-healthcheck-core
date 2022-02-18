<?php

namespace App\DTO\Mapper;

use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\Permission;

class InviteMapper
{
    /**
     * @param Permission $permission
     * @return InviteDTO
     */
    public static function createFromEntity(Permission $permission): InviteDTO
    {
        $inviteDTO = new InviteDTO();
        $inviteDTO->setId($permission->getId());
        $inviteDTO->setEmail($permission->getEmail());
        $inviteDTO->setGroupName($permission->getGroup()->getName());
        $inviteDTO->setExpirationDate($permission->getExpirationDate()->format('Y-m-d'));
        $inviteDTO->setPermissionType($permission->getPermissionType()->getKey());

        return $inviteDTO;
    }
}

<?php

namespace App\DTO\Mapper;

use App\DTO\Model\InviteDTO;
use App\Entity\Security\Permission;

class InviteMapper
{
    /**
     * @param Permission $permission
     * @return InviteDTO
     */
    public static function createFromEntity(Permission $permission): InviteDTO
    {
        $email = $permission->getEmail();
        if (is_null($email)) {
            $email = sprintf('[id %s] / %s', $permission->getPerson()->getId(), $permission->getPerson()->getNickname());
        }

        $inviteDTO = new InviteDTO();
        $inviteDTO->setId($permission->getId());
        $inviteDTO->setEmail($email);
        $inviteDTO->setGroupName($permission->getGroup()->getName());
        $inviteDTO->setExpirationDate($permission->getExpirationDate() ? $permission->getExpirationDate()->format('Y-m-d') : null);
        $inviteDTO->setPermissionType($permission->getPermissionType()->getKey());

        return $inviteDTO;
    }
}

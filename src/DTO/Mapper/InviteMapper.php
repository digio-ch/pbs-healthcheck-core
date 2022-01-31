<?php

namespace App\DTO\Mapper;

use App\DTO\Model\InviteDTO;
use App\Entity\Group;
use App\Entity\Permission;

class InviteMapper
{
    /**
     * @param Permission $invite
     * @return InviteDTO
     */
    public static function createFromEntity(Permission $invite): InviteDTO
    {
        $inviteDTO = new InviteDTO();
        $inviteDTO->setId($invite->getId());
        $inviteDTO->setEmail($invite->getEmail());
        $inviteDTO->setGroupName($invite->getGroup()->getName());
        $inviteDTO->setExpirationDate($invite->getExpirationDate()->format('Y-m-d'));

        return $inviteDTO;
    }
}

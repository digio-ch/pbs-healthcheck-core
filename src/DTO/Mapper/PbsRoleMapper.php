<?php

namespace App\DTO\Mapper;

use App\DTO\Model\PbsRoleDTO;

class PbsRoleMapper
{
    /**
     * @param array<string, mixed> $role
     */
    public static function createFromArray(array $role): PbsRoleDTO
    {
        return new PbsRoleDTO($role['group_id'], $role['group_name'], $role['role_type']);
    }
}

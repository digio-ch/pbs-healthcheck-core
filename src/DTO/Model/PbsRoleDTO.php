<?php

declare(strict_types=1);

namespace App\DTO\Model;

class PbsRoleDTO
{
    private int $groupId;

    private string $groupName;

    private string $roleType;

    /**
     * PbsRoleDTO constructor.
     */
    public function __construct(int $groupId, string $groupName, string $roleType)
    {
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->roleType = $roleType;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getRoleType(): string
    {
        return $this->roleType;
    }
}

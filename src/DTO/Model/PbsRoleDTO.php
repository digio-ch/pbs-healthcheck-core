<?php

namespace App\DTO\Model;

class PbsRoleDTO
{
    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var string
     */
    private $roleType;

    /**
     * PbsRoleDTO constructor.
     * @param int $groupId
     * @param string $groupName
     * @param string $roleType
     */
    public function __construct(int $groupId, string $groupName, string $roleType)
    {
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->roleType = $roleType;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @return string
     */
    public function getRoleType(): string
    {
        return $this->roleType;
    }
}

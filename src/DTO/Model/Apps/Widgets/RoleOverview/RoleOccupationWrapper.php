<?php

namespace App\DTO\Model\Apps\Widgets\RoleOverview;

class RoleOccupationWrapper
{
    /**
     * @var string
     */
    private string $role;

    private string $roleType;

    /**
     * @var string[]
     */
    private array $colors;

    /**
     * @var RoleOccupation[]
     */
    private array $data = [];

    /**
     * @param string $role
     * @param string[] $colors
     */
    public function __construct(string $role, string $roleType, array $colors)
    {
        $this->role = $role;
        $this->roleType = $roleType;
        $this->colors = $colors;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string[]
     */
    public function getColors(): array
    {
        return $this->colors;
    }

    /**
     * @param string[] $colors
     */
    public function setColors(array $colors): void
    {
        $this->colors = $colors;
    }

    /**
     * @return RoleOccupation[]|null
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param RoleOccupation[]|null $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(RoleOccupation $occupation): void
    {
        $this->data[] = $occupation;
    }

    /**
     * @return string
     */
    public function getRoleType(): string
    {
        return $this->roleType;
    }

    /**
     * @param string $roleType
     */
    public function setRoleType(string $roleType): void
    {
        $this->roleType = $roleType;
    }
}

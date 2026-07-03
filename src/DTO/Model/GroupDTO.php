<?php

namespace App\DTO\Model;

class GroupDTO
{
    private ?int $id;
    private ?string $cantonName;
    private ?string $name;
    private ?string $createdAt;
    private ?string $deletedAt;
    private ?GroupTypeDTO $groupType;
    private string $permissionType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCantonName(): ?string
    {
        return $this->cantonName;
    }

    public function setCantonName(?string $cantonName): void
    {
        $this->cantonName = $cantonName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return GroupTypeDTO
     */
    public function getGroupType(): ?GroupTypeDTO
    {
        return $this->groupType;
    }

    public function setGroupType(GroupTypeDTO $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function getPermissionType(): string
    {
        return $this->permissionType;
    }

    public function setPermissionType(string $permissionType): void
    {
        $this->permissionType = $permissionType;
    }
}

<?php

namespace App\DTO\Model;

class GroupDTO
{
    /** @var int|null */
    private ?int $id;
    /** @var string|null */
    private ?string $cantonName;
    /** @var string|null */
    private ?string $name;
    /** @var string|null */
    private ?string $createdAt;
    /** @var string|null */
    private ?string $deletedAt;
    /** @var GroupTypeDTO|null */
    private ?GroupTypeDTO $groupType;
    /** @var string $permissionType */
    private string $permissionType;

    /**
     * GroupDTO constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getCantonName(): ?string
    {
        return $this->cantonName;
    }

    /**
     * @param string|null $cantonName
     */
    public function setCantonName(?string $cantonName): void
    {
        $this->cantonName = $cantonName;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @param string|null $createdAt
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string|null
     */
    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    /**
     * @param string|null $deletedAt
     */
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

    /**
     * @param GroupTypeDTO $groupType
     */
    public function setGroupType(GroupTypeDTO $groupType): void
    {
        $this->groupType = $groupType;
    }

    /**
     * @return string
     */
    public function getPermissionType(): string
    {
        return $this->permissionType;
    }

    /**
     * @param string $permissionType
     */
    public function setPermissionType(string $permissionType): void
    {
        $this->permissionType = $permissionType;
    }
}

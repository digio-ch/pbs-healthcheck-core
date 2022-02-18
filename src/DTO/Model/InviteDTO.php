<?php

namespace App\DTO\Model;

use Symfony\Component\Validator\Constraints as Assert;

class InviteDTO
{
    /** @var int $id */
    private int $id;

    /**
     * @Assert\NotBlank(message="Email can't be empty")
     * @Assert\Email(message="Invalid email address")
     * @var string
     */
    private string $email;

    /** @var string $groupName */
    private string $groupName;

    /** @var string $expirationDate */
    private string $expirationDate;

    /** @var string $permissionType */
    private string $permissionType;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return string
     */
    public function getExpirationDate(): string
    {
        return $this->expirationDate;
    }

    /**
     * @param string $expirationDate
     */
    public function setExpirationDate(string $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
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

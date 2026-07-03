<?php

namespace App\DTO\Model;

use Symfony\Component\Validator\Constraints as Assert;

class InviteDTO
{
    private int $id;

    #[Assert\NotBlank(message: "Email can't be empty")]
    #[Assert\Email(message: 'Invalid email address')]
    private string $email;

    private ?string $expirationDate;

    private string $permissionType;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?string $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
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

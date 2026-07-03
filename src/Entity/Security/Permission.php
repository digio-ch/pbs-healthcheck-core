<?php

namespace App\Entity\Security;

use Doctrine\DBAL\Types\Types;
use DateTimeImmutable;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\Security\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_security_permission')]
#[ORM\Index(columns: ['email'])]
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $person = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['default' => null])]
    private ?string $email = null;

    #[ORM\JoinColumn(name: 'permission_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: PermissionType::class)]
    private PermissionType $permissionType;

    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    private Group $group;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $expirationDate;

    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $owner = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['default' => null])]
    private ?string $ownerEmail = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => null])]
    private ?bool $preExpiryNotified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPermissionType(): PermissionType
    {
        return $this->permissionType;
    }

    public function setPermissionType(PermissionType $permissionType): void
    {
        $this->permissionType = $permissionType;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?DateTimeImmutable $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getOwner(): ?Person
    {
        return $this->owner;
    }

    public function setOwner(?Person $owner): void
    {
        $this->owner = $owner;
    }

    public function getOwnerEmail(): ?string
    {
        return $this->ownerEmail;
    }

    public function setOwnerEmail(?string $ownerEmail): void
    {
        $this->ownerEmail = $ownerEmail;
    }

    public function getPreExpiryNotified(): ?bool
    {
        return $this->preExpiryNotified;
    }

    public function setPreExpiryNotified(?bool $preExpiryNotified): void
    {
        $this->preExpiryNotified = $preExpiryNotified;
    }
}

<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Repository\Midata\PersonRoleRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person_role')]
#[ORM\Index(columns: ['created_at'])]
#[ORM\Index(columns: ['deleted_at'])]
#[ORM\Entity(repositoryClass: PersonRoleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PersonRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'personRoles')]
    private ?Group $group = null;

    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $person = null;

    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Role::class)]
    private ?Role $role = null;

    /***
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function setGroup(?Group $group): void
    {
        $this->group = $group;
    }

    /**
     * @return Group
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }

    /**
     * @param Role|null $role
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }
}

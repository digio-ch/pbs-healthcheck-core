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
    private $name;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $deletedAt;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable|null $createdAt
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTimeImmutable|null $deletedAt
     */
    public function setDeletedAt(?DateTimeImmutable $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @param Person|null $person
     */
    public function setPerson(?Person $person)
    {
        $this->person = $person;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @param Group|null $group
     */
    public function setGroup(?Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Role|null $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }
}

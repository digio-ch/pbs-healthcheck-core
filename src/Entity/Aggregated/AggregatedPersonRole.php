<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Entity\Midata\PersonRole;
use App\Entity\Midata\Role;
use App\Repository\Aggregated\AggregatedPersonRoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AggregatedPersonRoleRepository::class)]
class AggregatedPersonRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    private ?Role $role = null;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    private ?Group $group = null;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    private ?Person $person = null;

    #[ORM\ManyToOne(targetEntity: PersonRole::class)]
    private ?PersonRole $midata = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $start_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $end_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getMidata(): ?PersonRole
    {
        return $this->midata;
    }

    public function setMidata(?PersonRole $midata): self
    {
        $this->midata = $midata;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getStartAt(): ?DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(?DateTimeInterface $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }
}

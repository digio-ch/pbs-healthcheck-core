<?php

namespace App\Entity\Gamification;

use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\Gamification\LoginRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_gamification_login')]
#[ORM\Entity(repositoryClass: LoginRepository::class)]
class Login
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $is_group_change = null;

    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'logins')]
    private ?Person $person = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'logins')]
    private ?Group $group = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $hashed_person_id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private string $role;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIsGroupChange(): ?bool
    {
        return $this->is_group_change;
    }

    public function setIsGroupChange(bool $group_change): self
    {
        $this->is_group_change = $group_change;

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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getHashedPersonId(): ?string
    {
        return $this->hashed_person_id;
    }

    public function setHashedPersonId(?string $hashed_person_id): self
    {
        $this->hashed_person_id = $hashed_person_id;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }
}

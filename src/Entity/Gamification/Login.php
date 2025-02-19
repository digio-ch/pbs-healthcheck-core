<?php

namespace App\Entity\Gamification;

use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\Gamification\LoginRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoginRepository::class)
 */
class Login
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_group_change;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="logins")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="logins")
     * @ORM\JoinColumn(nullable=true)
     */
    private $group;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hashed_person_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $role;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
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

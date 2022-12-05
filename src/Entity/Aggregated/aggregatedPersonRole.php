<?php

namespace App\Entity\Aggregated;

use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Entity\Midata\PersonRole;
use App\Entity\Midata\Role;
use App\Repository\Aggregated\aggregatedPersonRoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=aggregatedPersonRoleRepository::class)
 * @ORM\Table(name="hc_aggregated_leader_overview_leader")
 */
class aggregatedPersonRole
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class)
     */
    private $person_id;

    /**
     * @ORM\ManyToOne(targetEntity=Role::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $role_id;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\Column(type="date")
     */
    private $start_at;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $end_at;

    /**
     * @ORM\ManyToOne(targetEntity=PersonRole::class)
     */
    private $midata_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonId(): ?Person
    {
        return $this->person_id;
    }

    public function setPersonId(?Person $person_id): self
    {
        $this->person_id = $person_id;

        return $this;
    }

    public function getRoleId(): ?Role
    {
        return $this->role_id;
    }

    public function setRoleId(?Role $role_id): self
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getGroupId(): ?Group
    {
        return $this->group_id;
    }

    public function setGroupId(?Group $group_id): self
    {
        $this->group_id = $group_id;

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

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeInterface $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function getMidataId(): ?PersonRole
    {
        return $this->midata_id;
    }

    public function setMidataId(?PersonRole $midata_id): self
    {
        $this->midata_id = $midata_id;

        return $this;
    }
}

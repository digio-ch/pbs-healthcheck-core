<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_person_role", indexes={
 *     @ORM\Index(columns={"created_at"}),
 *     @ORM\Index(columns={"deleted_at"})
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"midata_id", "sync_group_id"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PersonRoleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PersonRole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $midataId;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="sync_group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $syncGroup;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="personRoles")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

    /***
     * @ORM\Column(type="string", length="255", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
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
     * @return mixed
     */
    public function getMidataId()
    {
        return $this->midataId;
    }

    /**
     * @param int $midataId
     */
    public function setMidataId(int $midataId)
    {
        $this->midataId = $midataId;
    }

    public function getSyncGroup(): Group
    {
        return $this->syncGroup;
    }

    public function setSyncGroup($syncGroup)
    {
        $this->syncGroup = $syncGroup;
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
    public function setRole(?Role $role)
    {
        $this->role = $role;
    }

    /**
     * @return Role|null
     */
    public function getRole(): Role
    {
        return $this->role;
    }
}

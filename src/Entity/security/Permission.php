<?php

namespace App\Entity\security;

use App\Entity\midata\Group;
use App\Entity\midata\Person;
use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_security_permission", indexes={
 *     @ORM\Index(columns={"email"})
 * })
 * @ORM\Entity(repositoryClass=PermissionRepository::class)
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class)
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    private ?Person $person = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": null})
     */
    private ?string $email = null;

    /**
     * @ORM\ManyToOne(targetEntity=PermissionType::class)
     * @ORM\JoinColumn(name="permission_type_id", referencedColumnName="id")
     */
    private PermissionType $permissionType;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private Group $group;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $expirationDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Person|null
     */
    public function getPerson(): ?Person
    {
        return $this->person;
    }

    /**
     * @param Person|null $person
     */
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

    /**
     * @return PermissionType
     */
    public function getPermissionType(): PermissionType
    {
        return $this->permissionType;
    }

    /**
     * @param PermissionType $permissionType
     */
    public function setPermissionType(PermissionType $permissionType): void
    {
        $this->permissionType = $permissionType;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}

<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_group", indexes={
 *     @ORM\Index(columns={"name"}),
 *     @ORM\Index(columns={"created_at"}),
 *     @ORM\Index(columns={"deleted_at"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parentGroup")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="children")
     * @ORM\JoinColumn(name="parent_group_id", referencedColumnName="id")
     */
    private $parentGroup;

    /**
     * @ORM\OneToMany(targetEntity="EventGroup", mappedBy="group", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $events;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantonId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cantonName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="GroupType")
     * @ORM\JoinColumn(name="group_type_id", referencedColumnName="id")
     */
    private $groupType;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PersonRole", mappedBy="group")
     */
    private $personRoles;

    /**
     * @ORM\OneToMany(targetEntity=GeoLocation::class, mappedBy="abteilung", orphanRemoval=true)
     */
    private $geoLocations;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->geoLocations = new ArrayCollection();
    }

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
     * @return Group|null
     */
    public function getParentGroup(): ?Group
    {
        return $this->parentGroup;
    }

    /**
     * @param Group|null $group
     */
    public function setParentGroup(?Group $group)
    {
        $this->parentGroup = $group;
    }

    /**
     * @return int|null
     */
    public function getCantonId(): ?int
    {
        return $this->cantonId;
    }

    /**
     * @param int|null $cantonId
     */
    public function setCantonId(?int $cantonId)
    {
        $this->cantonId = $cantonId;
    }

    /**
     * @return null|string
     */
    public function getCantonName(): ?string
    {
        return $this->cantonName;
    }

    /**
     * @param null|string $cantonName
     */
    public function setCantonName(?string $cantonName)
    {
        $this->cantonName = $cantonName;
    }

    /**
     * @return GroupType|null
     */
    public function getGroupType(): ?GroupType
    {
        return $this->groupType;
    }

    /**
     * @param GroupType|null $groupType
     */
    public function setGroupType(?GroupType $groupType)
    {
        $this->groupType = $groupType;
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * @return Collection|GeoLocation[]
     */
    public function getGeoLocations(): Collection
    {
        return $this->geoLocations;
    }

    public function addGeoLocation(GeoLocation $geoLocation): self
    {
        if (!$this->geoLocations->contains($geoLocation)) {
            $this->geoLocations[] = $geoLocation;
            $geoLocation->setAbteilung($this);
        }

        return $this;
    }

    public function removeGeoLocation(GeoLocation $geoLocation): self
    {
        if ($this->geoLocations->removeElement($geoLocation)) {
            // set the owning side to null (unless already changed)
            if ($geoLocation->getAbteilung() === $this) {
                $geoLocation->setAbteilung(null);
            }
        }

        return $this;
    }
}

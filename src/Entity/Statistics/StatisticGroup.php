<?php

namespace App\Entity\Statistics;

use App\Entity\Midata\GroupType;
use App\Repository\Statistics\StatisticGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatisticGroupRepository::class)
 */
class StatisticGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=StatisticGroup::class, inversedBy="children")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent_group;

    /**
     * @ORM\OneToMany(targetEntity=StatisticGroup::class, mappedBy="parent_group")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity=GroupType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=StatisticGroup::class)
     */
    private $canton;


    /**
     * @ORM\OneToMany(targetEntity=GroupGeoLocation::class, mappedBy="group")
     */
    private $geoLocations;


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->geoLocations = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentGroup(): ?self
    {
        return $this->parent_group;
    }

    public function setParentGroup(?self $parent_group): self
    {
        $this->parent_group = $parent_group;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): ?Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParentGroup($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParentGroup() === $this) {
                $child->setParentGroup(null);
            }
        }

        return $this;
    }

    public function getGroupType(): ?GroupType
    {
        return $this->group_type;
    }

    public function setGroupType(?GroupType $group_type): self
    {
        $this->group_type = $group_type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCanton(): ?self
    {
        return $this->canton;
    }

    public function setCanton(?self $canton): self
    {
        $this->canton = $canton;

        return $this;
    }


    /**
     * @return Collection<int, GroupGeoLocation>
     */
    public function getGeoLocations(): Collection
    {
        return $this->geoLocations;
    }

    public function addGeoLocation(GroupGeoLocation $geoLocation): self
    {
        if (!$this->geoLocations->contains($geoLocation)) {
            $this->geoLocations[] = $geoLocation;
            $geoLocation->setGroups($this);
        }

        return $this;
    }

    public function removeGeoLocation(GroupGeoLocation $geoLocation): self
    {
        if ($this->geoLocations->removeElement($geoLocation)) {
            // set the owning side to null (unless already changed)
            if ($geoLocation->getGroups() === $this) {
                $geoLocation->setGroups(null);
            }
        }

        return $this;
    }


}

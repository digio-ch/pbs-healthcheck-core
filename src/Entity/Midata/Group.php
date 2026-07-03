<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Gamification\Login;
use App\Entity\General\GroupSettings;
use App\Repository\Midata\GroupRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_group')]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['created_at'])]
#[ORM\Index(columns: ['deleted_at'])]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'parentGroup', targetEntity: Group::class)]
    private $children;

    #[ORM\JoinColumn(name: 'parent_group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'children')]
    private ?Group $parentGroup = null;

    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: EventGroup::class, cascade: ['persist', 'remove'])]
    private $events;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $cantonId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $cantonName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    #[ORM\JoinColumn(name: 'group_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: GroupType::class)]
    private ?GroupType $groupType = null;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: PersonRole::class)]
    private $personRoles;

    #[ORM\OneToOne(mappedBy: 'group', targetEntity: GroupSettings::class, cascade: ['persist', 'remove'])]
    private ?GroupSettings $groupSettings = null;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Login::class)]
    private $logins;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: GamificationQuapEvent::class)]
    private $gamificationQuapEvents;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->logins = new ArrayCollection();
        $this->gamificationQuapEvents = new ArrayCollection();
    }

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

    public function getParentGroup(): ?Group
    {
        return $this->parentGroup;
    }

    public function setParentGroup(?Group $group): void
    {
        $this->parentGroup = $group;
    }

    public function getCantonId(): ?int
    {
        return $this->cantonId;
    }

    public function setCantonId(?int $cantonId): void
    {
        $this->cantonId = $cantonId;
    }

    public function getCantonName(): ?string
    {
        return $this->cantonName;
    }

    public function setCantonName(?string $cantonName): void
    {
        $this->cantonName = $cantonName;
    }

    public function getGroupType(): ?GroupType
    {
        return $this->groupType;
    }

    public function setGroupType(?GroupType $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

    public function getGroupSettings(): ?GroupSettings
    {
        return $this->groupSettings;
    }

    public function setGroupSettings(?GroupSettings $groupSettings): self
    {
        // unset the owning side of the relation if necessary
        if ($groupSettings === null && $this->groupSettings !== null) {
            $this->groupSettings->setGroup(null);
        }

        // set the owning side of the relation if necessary
        if ($groupSettings !== null && $groupSettings->getGroup() !== $this) {
            $groupSettings->setGroup($this);
        }

        $this->roleOverviewFilter = $groupSettings;

        return $this;
    }

    /**
     * @return Collection<int, Login>
     */
    public function getLogins(): Collection
    {
        return $this->logins;
    }

    public function addLogin(Login $login): self
    {
        if (!$this->logins->contains($login)) {
            $this->logins[] = $login;
            $login->setGgroup($this);
        }

        return $this;
    }

    public function removeLogin(Login $login): self
    {
        if ($this->logins->removeElement($login)) {
            // set the owning side to null (unless already changed)
            if ($login->getGgroup() === $this) {
                $login->setGgroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GamificationQuapEvent>
     */
    public function getGamificationQuapEvents(): Collection
    {
        return $this->gamificationQuapEvents;
    }

    public function addGamificationQuapEvent(GamificationQuapEvent $gamificationQuapEvent): self
    {
        if (!$this->gamificationQuapEvents->contains($gamificationQuapEvent)) {
            $this->gamificationQuapEvents[] = $gamificationQuapEvent;
            $gamificationQuapEvent->setGroup($this);
        }

        return $this;
    }

    public function removeGamificationQuapEvent(GamificationQuapEvent $gamificationQuapEvent): self
    {
        if ($this->gamificationQuapEvents->removeElement($gamificationQuapEvent)) {
            // set the owning side to null (unless already changed)
            if ($gamificationQuapEvent->getGroup() === $this) {
                $gamificationQuapEvent->setGroup(null);
            }
        }

        return $this;
    }
}

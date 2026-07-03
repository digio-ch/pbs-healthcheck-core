<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use App\Entity\Admin\GeoAddress;
use App\Entity\Gamification\GamificationQuapEvent;
use App\Entity\Gamification\LevelUpLog;
use App\Entity\Gamification\Login;
use App\Entity\Gamification\GamificationPersonProfile;
use App\Repository\Midata\PersonRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person')]
#[ORM\Index(columns: ['nickname'])]
#[ORM\Index(columns: ['gender'])]
#[ORM\Index(columns: ['birthday'])]
#[ORM\Index(columns: ['entry_date'])]
#[ORM\Index(columns: ['leaving_date'])]
#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Person
{
    public const GENDER_M = 'm';
    public const GENDER_F = 'w';
    public const GENDER_U = '';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $pbsNumber = null;

    #[ORM\Column(type: Types::STRING, length: 1, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeInterface $birthday = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $zip = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private DateTimeInterface|null|DateTimeImmutable $entryDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private DateTimeInterface|null|DateTimeImmutable $leavingDate = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    private ?Group $group = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: PersonEvent::class, cascade: ['persist', 'remove'])]
    private $events;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: PersonQualification::class, cascade: ['persist', 'remove'])]
    private $qualifications;

    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: GeoAddress::class, inversedBy: 'people')]
    private ?GeoAddress $geoAddress = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Login::class)]
    private $logins;

    #[ORM\OneToOne(mappedBy: 'person', targetEntity: GamificationPersonProfile::class, cascade: ['persist', 'remove'])]
    private ?GamificationPersonProfile $gamification = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: LevelUpLog::class, cascade: ['persist', 'remove'])]
    private $levelUps;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: GamificationQuapEvent::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $gamificationQuapEvents;

    public function __construct()
    {
        $this->logins = new ArrayCollection();
        $this->levelUps = new ArrayCollection();
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

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getPbsNumber(): ?string
    {
        return $this->pbsNumber;
    }

    public function setPbsNumber(?string $pbsNumber): void
    {
        $this->pbsNumber = $pbsNumber;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    public function getBirthday(): ?DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?DateTimeInterface $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): void
    {
        $this->town = $town;
    }

    public function getZip(): ?int
    {
        return $this->zip;
    }

    public function setZip(?int $zip): void
    {
        $this->zip = $zip;
    }

    public function getEntryDate(): ?DateTimeImmutable
    {
        return $this->entryDate;
    }

    public function setEntryDate(?DateTimeInterface $entryDate): void
    {
        $this->entryDate = $entryDate;
    }

    public function getLeavingDate(): ?DateTimeImmutable
    {
        return $this->leavingDate;
    }

    public function setLeavingDate(?DateTimeInterface $leavingDate): void
    {
        $this->leavingDate = $leavingDate;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): void
    {
        $this->group = $group;
    }

    public function getGeoAddress(): ?GeoAddress
    {
        return $this->geoAddress;
    }

    public function setGeoAddress(GeoAddress $geoAddress): void
    {
        $this->geoAddress = $geoAddress;
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
            $login->setPerson($this);
        }

        return $this;
    }

    public function removeLogin(Login $login): self
    {
        if ($this->logins->removeElement($login)) {
            // set the owning side to null (unless already changed)
            if ($login->getPerson() === $this) {
                $login->setPerson(null);
            }
        }

        return $this;
    }

    public function getGamification(): ?GamificationPersonProfile
    {
        return $this->gamification;
    }

    public function setGamification(GamificationPersonProfile $gamification): self
    {
        // set the owning side of the relation if necessary
        if ($gamification->getPerson() !== $this) {
            $gamification->setPerson($this);
        }

        $this->gamification = $gamification;

        return $this;
    }

    /**
     * @return Collection<int, LevelUpLog>
     */
    public function getLevelUps(): Collection
    {
        return $this->levelUps;
    }

    public function addLevelUp(LevelUpLog $displayed): self
    {
        if (!$this->levelUps->contains($displayed)) {
            $this->levelUps[] = $displayed;
            $displayed->setPerson($this);
        }

        return $this;
    }

    public function removeLevelUp(LevelUpLog $displayed): self
    {
        if ($this->levelUps->removeElement($displayed)) {
            // set the owning side to null (unless already changed)
            if ($displayed->getPerson() === $this) {
                $displayed->setPerson(null);
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
            $gamificationQuapEvent->setPerson($this);
        }

        return $this;
    }

    public function removeGamificationQuapEvent(GamificationQuapEvent $gamificationQuapEvent): self
    {
        if ($this->gamificationQuapEvents->removeElement($gamificationQuapEvent)) {
            // set the owning side to null (unless already changed)
            if ($gamificationQuapEvent->getPerson() === $this) {
                $gamificationQuapEvent->setPerson(null);
            }
        }

        return $this;
    }
}

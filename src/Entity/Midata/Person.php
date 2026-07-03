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
    private $birthday;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $zip = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $entryDate;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $leavingDate;

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
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param null|string $nickname
     */
    public function setNickname(?string $nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return null|string
     */
    public function getPbsNumber(): ?string
    {
        return $this->pbsNumber;
    }

    /**
     * @param null|string $pbsNumber
     */
    public function setPbsNumber(?string $pbsNumber)
    {
        $this->pbsNumber = $pbsNumber;
    }

    /**
     * @return null|string
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param null|string $gender
     */
    public function setGender(?string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getBirthday(): ?DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param DateTimeInterface|null $birthday
     */
    public function setBirthday(?DateTimeInterface $birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return null|string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     */
    public function setAddress(?string $address)
    {
        $this->address = $address;
    }

    /**
     * @return null|string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param null|string $country
     */
    public function setCountry(?string $country)
    {
        $this->country = $country;
    }

    /**
     * @return null|string
     */
    public function getTown(): ?string
    {
        return $this->town;
    }

    /**
     * @param null|string $town
     */
    public function setTown(?string $town)
    {
        $this->town = $town;
    }

    /**
     * @return int|null
     */
    public function getZip(): ?int
    {
        return $this->zip;
    }

    /**
     * @param int|null $zip
     */
    public function setZip(?int $zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEntryDate(): ?DateTimeImmutable
    {
        return $this->entryDate;
    }

    /**
     * @param DateTimeInterface|null $entryDate
     */
    public function setEntryDate(?DateTimeInterface $entryDate)
    {
        $this->entryDate = $entryDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getLeavingDate(): ?DateTimeImmutable
    {
        return $this->leavingDate;
    }

    /**
     * @param DateTimeInterface|null $leavingDate
     */
    public function setLeavingDate(?DateTimeInterface $leavingDate)
    {
        $this->leavingDate = $leavingDate;
    }

    /**
     * @return Group|null
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }

    /**
     * @param Group|null $group
     */
    public function setGroup(?Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return GeoAddress|null
     */
    public function getGeoAddress(): ?GeoAddress
    {
        return $this->geoAddress;
    }

    /**
     * @param GeoAddress $geoAddress
     */
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

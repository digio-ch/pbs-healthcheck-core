<?php

namespace App\Entity\Midata;

use App\Entity\Admin\GeoAddress;
use App\Entity\Gamification\Login;
use App\Entity\Gamification\PersonGoal;
use App\Repository\Midata\PersonRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_person", indexes={
 *     @ORM\Index(columns={"nickname"}),
 *     @ORM\Index(columns={"gender"}),
 *     @ORM\Index(columns={"birthday"}),
 *     @ORM\Index(columns={"entry_date"}),
 *     @ORM\Index(columns={"leaving_date"})
 * })
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Person
{
    public const GENDER_M = 'm';
    public const GENDER_F = 'w';
    public const GENDER_U = '';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pbsNumber;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $town;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $zip;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $entryDate;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $leavingDate;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $group;

    /**
     * @ORM\OneToMany(targetEntity=PersonEvent::class, mappedBy="person", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=PersonQualification::class, mappedBy="person", cascade={"persist", "remove"})
     */
    private $qualifications;

    /**
     * @ORM\ManyToOne(targetEntity=GeoAddress::class, inversedBy="people")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $geoAddress;

    /**
     * @ORM\OneToMany(targetEntity=Login::class, mappedBy="person")
     */
    private $logins;

    /**
     * @ORM\OneToOne(targetEntity=PersonGoal::class, mappedBy="person", cascade={"persist", "remove"})
     */
    private $gamification;

    public function __construct()
    {
        $this->logins = new ArrayCollection();
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

    public function getGamification(): ?PersonGoal
    {
        return $this->gamification;
    }

    public function setGamification(PersonGoal $gamification): self
    {
        // set the owning side of the relation if necessary
        if ($gamification->getPerson() !== $this) {
            $gamification->setPerson($this);
        }

        $this->gamification = $gamification;

        return $this;
    }
}

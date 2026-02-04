<?php

namespace App\Entity\General;

use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\General\PersonSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_person_settings")
 * @ORM\Entity(repositoryClass=PersonSettingsRepository::class)
 */
class PersonSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private Group $group;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private Person $person;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $censusFilterRoles;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $censusFilterGroups;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $censusFilterMales;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $censusFilterFemales;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    /**
     * @return array|null
     */
    public function getCensusFilterRoles(): ?array
    {
        return $this->censusFilterRoles;
    }

    /**
     * @param array|null $censusFilterRoles
     */
    public function setCensusFilterRoles(?array $censusFilterRoles): void
    {
        $this->censusFilterRoles = $censusFilterRoles;
    }

    /**
     * @return array|null
     */
    public function getCensusFilterGroups(): ?array
    {
        return $this->censusFilterGroups;
    }

    /**
     * @param array|null $censusFilterGroups
     */
    public function setCensusFilterGroups(?array $censusFilterGroups): void
    {
        $this->censusFilterGroups = $censusFilterGroups;
    }

    /**
     * @return bool|null
     */
    public function getCensusFilterMales(): ?bool
    {
        return $this->censusFilterMales;
    }

    /**
     * @param bool|null $censusFilterMales
     */
    public function setCensusFilterMales(?bool $censusFilterMales): void
    {
        $this->censusFilterMales = $censusFilterMales;
    }

    /**
     * @return bool|null
     */
    public function getCensusFilterFemales(): ?bool
    {
        return $this->censusFilterFemales;
    }

    /**
     * @param bool|null $censusFilterFemales
     */
    public function setCensusFilterFemales(?bool $censusFilterFemales): void
    {
        $this->censusFilterFemales = $censusFilterFemales;
    }
}

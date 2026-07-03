<?php

namespace App\Entity\General;

use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\Group;
use App\Entity\Midata\Person;
use App\Repository\General\PersonSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_person_settings')]
#[ORM\Entity(repositoryClass: PersonSettingsRepository::class)]
class PersonSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    private Group $group;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'events')]
    private Person $person;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $censusFilterRoles;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $censusFilterGroups;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $censusFilterMales;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $censusFilterFemales;

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    public function getCensusFilterRoles(): ?array
    {
        return $this->censusFilterRoles;
    }

    public function setCensusFilterRoles(?array $censusFilterRoles): void
    {
        $this->censusFilterRoles = $censusFilterRoles;
    }

    public function getCensusFilterGroups(): ?array
    {
        return $this->censusFilterGroups;
    }

    public function setCensusFilterGroups(?array $censusFilterGroups): void
    {
        $this->censusFilterGroups = $censusFilterGroups;
    }

    public function getCensusFilterMales(): ?bool
    {
        return $this->censusFilterMales;
    }

    public function setCensusFilterMales(?bool $censusFilterMales): void
    {
        $this->censusFilterMales = $censusFilterMales;
    }

    public function getCensusFilterFemales(): ?bool
    {
        return $this->censusFilterFemales;
    }

    public function setCensusFilterFemales(?bool $censusFilterFemales): void
    {
        $this->censusFilterFemales = $censusFilterFemales;
    }
}

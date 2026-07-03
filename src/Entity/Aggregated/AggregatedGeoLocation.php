<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedGeoLocationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_geo_location')]
#[ORM\Index(columns: ['longitude'])]
#[ORM\Index(columns: ['latitude'])]
#[ORM\Index(columns: ['label'])]
#[ORM\Index(columns: ['shape'])]
#[ORM\Index(columns: ['group_type'])]
#[ORM\Index(columns: ['person_type'])]
#[ORM\Index(columns: ['data_point_date'])]
#[ORM\Entity(repositoryClass: AggregatedGeoLocationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedGeoLocation extends AggregatedEntity
{
    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $shape = 'circle';

    #[ORM\Column(type: Types::STRING)]
    private ?string $groupType = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $personType = null;

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getShape(): ?string
    {
        return $this->shape;
    }

    public function setShape(string $shape): void
    {
        $this->shape = $shape;
    }

    public function getGroupType(): string
    {
        return $this->groupType;
    }

    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function getPersonType(): ?string
    {
        return $this->personType;
    }

    public function setPersonType(?string $personType): void
    {
        $this->personType = $personType;
    }
}

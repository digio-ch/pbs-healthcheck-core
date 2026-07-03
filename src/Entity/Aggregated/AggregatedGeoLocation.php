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

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getShape(): ?string
    {
        return $this->shape;
    }

    /**
     * @param string $shape
     */
    public function setShape(string $shape): void
    {
        $this->shape = $shape;
    }

    /**
     * @return string
     */
    public function getGroupType(): string
    {
        return $this->groupType;
    }

    /**
     * @param string $groupType
     */
    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    /**
     * @return string|null
     */
    public function getPersonType(): ?string
    {
        return $this->personType;
    }

    /**
     * @param string|null $personType
     */
    public function setPersonType(?string $personType): void
    {
        $this->personType = $personType;
    }
}

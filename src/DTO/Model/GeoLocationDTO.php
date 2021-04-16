<?php

namespace App\DTO\Model;

class GeoLocationDTO
{
    /** @var float $longitude */
    private $longitude;

    /** @var float $latitude */
    private $latitude;

    /** @var string $label */
    private $label;

    /** @var GeoLocationTypeDTO $type */
    private $type;

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     */
    public function setLongitude(?float $longitude): void
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
     * @param float|null $latitude
     */
    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
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
     * @return GeoLocationTypeDTO
     */
    public function getType(): GeoLocationTypeDTO
    {
        return $this->type;
    }

    /**
     * @param GeoLocationTypeDTO $type
     */
    public function setType(GeoLocationTypeDTO $type): void
    {
        $this->type = $type;
    }
}

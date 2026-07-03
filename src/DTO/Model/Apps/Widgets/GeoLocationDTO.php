<?php

namespace App\DTO\Model\Apps\Widgets;

class GeoLocationDTO
{
    private ?float $longitude = null;

    private ?float $latitude = null;

    private ?string $label = null;

    private GeoLocationTypeDTO $type;

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getType(): GeoLocationTypeDTO
    {
        return $this->type;
    }

    public function setType(GeoLocationTypeDTO $type): void
    {
        $this->type = $type;
    }
}

<?php

namespace App\DTO\Model\Apps\Widgets;

class GeoLocationTypeDTO
{
    private string $shape;

    private string $color;

    public function getShape(): string
    {
        return $this->shape;
    }

    public function setShape(string $shape): void
    {
        $this->shape = $shape;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}

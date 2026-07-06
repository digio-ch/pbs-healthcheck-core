<?php

namespace App\DTO\Model\Charts;

class LineChartDataDTO
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var LineChartDataPointDTO[]
     */
    protected $series = [];

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return LineChartDataPointDTO[]
     */
    public function getSeries()
    {
        return $this->series;
    }

    public function addSeries(LineChartDataPointDTO ...$series): void
    {
        array_push($this->series, ...$series);
    }
}

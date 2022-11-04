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

    /**
     * @param string $name
     */
    public function setName(string $name)
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

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
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

    /**
     * @param LineChartDataPointDTO $series
     */
    public function addSeries(LineChartDataPointDTO $series)
    {
        $this->series[] = $series;
    }
}

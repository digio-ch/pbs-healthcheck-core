<?php

namespace App\DTO\Model\Charts;

class BarChartDataDTO
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var BarChartBarDataDTO[]
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
     * @return BarChartBarDataDTO[]
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param BarChartBarDataDTO $series
     */
    public function addSeries(BarChartBarDataDTO $series)
    {
        $this->series[] = $series;
    }
}

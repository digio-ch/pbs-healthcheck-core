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
     * @var bool|null $isSummed
     */
    protected ?bool $isSummed;

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
     * @param BarChartBarDataDTO ...$series
     */
    public function addSeries(BarChartBarDataDTO ...$series)
    {
        array_push($this->series, ...$series);
    }

    /**
     * @return bool|null
     */
    public function getIsSummed(): ?bool
    {
        return $this->isSummed;
    }

    /**
     * @param bool|null $isSummed
     */
    public function setIsSummed(?bool $isSummed): void
    {
        $this->isSummed = $isSummed;
    }
}

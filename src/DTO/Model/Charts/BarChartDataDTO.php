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

    protected ?bool $isSummed;

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

    /**
     * @return BarChartBarDataDTO[]
     */
    public function getSeries()
    {
        return $this->series;
    }

    public function addSeries(BarChartBarDataDTO ...$series): void
    {
        array_push($this->series, ...$series);
    }

    public function getIsSummed(): ?bool
    {
        return $this->isSummed;
    }

    public function setIsSummed(?bool $isSummed): void
    {
        $this->isSummed = $isSummed;
    }
}

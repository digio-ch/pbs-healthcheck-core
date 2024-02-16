<?php

namespace App\DTO\Model\Apps\Census;

class DevelopmentWidgetDTO
{
    /**
     * @var LineChartDataDTO[]
     */
    private array $absolute;
    /**
     * @var LineChartDataDTO[]
     */
    private array $relative;

    private array $years;

    /**
     * @return array
     */
    public function getYears(): array
    {
        return $this->years;
    }

    /**
     * @param array $years
     */
    public function setYears(array $years): void
    {
        $this->years = $years;
    }

    /**
     * @return array
     */
    public function getAbsolute(): array
    {
        return $this->absolute;
    }

    /**
     * @param array $absolute
     */
    public function setAbsolute(array $absolute): void
    {
        $this->absolute = $absolute;
    }

    /**
     * @return array
     */
    public function getRelative(): array
    {
        return $this->relative;
    }

    /**
     * @param array $relative
     */
    public function setRelative(array $relative): void
    {
        $this->relative = $relative;
    }
}

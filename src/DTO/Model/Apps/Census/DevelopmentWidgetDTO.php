<?php

declare(strict_types=1);

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

    /**
     * @var mixed[]
     */
    private array $years;

    /**
     * @return mixed[]
     */
    public function getYears(): array
    {
        return $this->years;
    }

    public function setYears(array $years): void
    {
        $this->years = $years;
    }

    /**
     * @return LineChartDataDTO[]
     */
    public function getAbsolute(): array
    {
        return $this->absolute;
    }

    public function setAbsolute(array $absolute): void
    {
        $this->absolute = $absolute;
    }

    /**
     * @return LineChartDataDTO[]
     */
    public function getRelative(): array
    {
        return $this->relative;
    }

    public function setRelative(array $relative): void
    {
        $this->relative = $relative;
    }
}

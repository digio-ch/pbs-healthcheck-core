<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Widgets;

use App\DTO\Model\Charts\BarChartDataDTO;
use App\DTO\Model\Charts\LineChartDataDTO;
use App\DTO\Model\Charts\PieChartDataDTO;

class ExcludeUnknownGenderChartDTO
{
    private int $unknownGenderCount = 0;

    /**
     * @var PieChartDataDTO|BarChartDataDTO|LineChartDataDTO|array
     */
    private $data;

    public function getUnknownGenderCount(): int
    {
        return $this->unknownGenderCount;
    }

    public function setUnknownGenderCount(int $unknownGenderCount): void
    {
        $this->unknownGenderCount = $unknownGenderCount;
    }

    /**
     * @return BarChartDataDTO|LineChartDataDTO|PieChartDataDTO|array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param BarChartDataDTO|LineChartDataDTO|PieChartDataDTO|array $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}

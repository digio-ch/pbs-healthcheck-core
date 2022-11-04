<?php

namespace App\DTO\Model\Apps\Widgets;

use App\DTO\Model\Charts\BarChartDataDTO;
use App\DTO\Model\Charts\LineChartDataDTO;
use App\DTO\Model\Charts\PieChartDataDTO;

class ExcludeUnknownGenderChartDTO
{
    /**
     * @var int
     */
    private $unknownGenderCount;

    /**
     * @var PieChartDataDTO|BarChartDataDTO|LineChartDataDTO|array
     */
    private $data;

    /**
     * @return int
     */
    public function getUnknownGenderCount(): int
    {
        return $this->unknownGenderCount;
    }

    /**
     * @param int $unknownGenderCount
     */
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

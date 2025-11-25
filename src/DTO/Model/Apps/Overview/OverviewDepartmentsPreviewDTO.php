<?php

namespace App\DTO\Model\Apps\Overview;

use App\DTO\Model\Charts\PieChartDataDTO;

class OverviewDepartmentsPreviewDTO
{
    /**
     * @var int $departments
     */
    private int $departments;

    /**
     * @var PieChartDataDTO[] $groupTypes;
     */
    private array $groupTypes;

    /**
     * @param int $departments
     * @param PieChartDataDTO[] $groupTypes
     */
    public function __construct(int $departments = 0, array $groupTypes = [])
    {
        $this->departments = $departments;
        $this->groupTypes = $groupTypes;
    }


    /**
     * @return int
     */
    public function getDepartments(): int
    {
        return $this->departments;
    }

    /**
     * @param int $departments
     * @return void
     */
    public function setDepartments(int $departments): void
    {
        $this->departments = $departments;
    }

    /**
     * @return PieChartDataDTO[]
     */
    public function getGroupTypes(): array
    {
        return $this->groupTypes;
    }

    /**
     * @param array $groupTypes
     * @return void
     */
    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }
}

<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Overview;

use App\DTO\Model\Charts\PieChartDataDTO;

class OverviewDepartmentsPreviewDTO
{
    private int $departments;

    /**
     * @var PieChartDataDTO[] $groupTypes;
     */
    private array $groupTypes;

    /**
     * @param PieChartDataDTO[] $groupTypes
     */
    public function __construct(int $departments = 0, array $groupTypes = [])
    {
        $this->departments = $departments;
        $this->groupTypes = $groupTypes;
    }


    public function getDepartments(): int
    {
        return $this->departments;
    }

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

    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }
}

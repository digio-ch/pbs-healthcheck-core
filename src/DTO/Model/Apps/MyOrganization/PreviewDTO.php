<?php

namespace App\DTO\Model\Apps\MyOrganization;

use App\DTO\Model\Charts\PieChartDataDTO;

class PreviewDTO
{
    /**
     * @var string[] $departments
     */
    private array $departments;

    /**
     * @var PieChartDataDTO[] $groupTypes;
     */
    private array $groupTypes;

    /**
     * @param string[] $departments
     * @param PieChartDataDTO[] $groupTypes
     */
    public function __construct(array $departments, array $groupTypes)
    {
        $this->departments = $departments;
        $this->groupTypes = $groupTypes;
    }

    /**
     * @return string[]
     */
    public function getDepartments(): array
    {
        return $this->departments;
    }

    /**
     * @param string[] $departments
     */
    public function setDepartments(array $departments): void
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
     * @param PieChartDataDTO[] $groupTypes
     */
    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }
}

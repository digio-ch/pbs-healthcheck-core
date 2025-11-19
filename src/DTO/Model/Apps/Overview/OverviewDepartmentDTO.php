<?php

namespace App\DTO\Model\Apps\Overview;

use App\DTO\Model\Charts\PieChartDataDTO;

class OverviewDepartmentDTO
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var PieChartDataDTO[] $groupTypes;
     */
    private array $groupTypes;

    /**
     * @param int $id
     * @param string $name
     * @param PieChartDataDTO[] $groupTypes
     */
    public function __construct(int $id, string $name, array $groupTypes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->groupTypes = $groupTypes;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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

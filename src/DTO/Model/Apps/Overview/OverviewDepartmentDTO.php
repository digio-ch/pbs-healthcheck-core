<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Overview;

use App\DTO\Model\Charts\PieChartDataDTO;

class OverviewDepartmentDTO
{
    private int $id;

    private string $name;

    /**
     * @var PieChartDataDTO[] $groupTypes;
     */
    private array $groupTypes;

    /**
     * @param PieChartDataDTO[] $groupTypes
     */
    public function __construct(int $id, string $name, array $groupTypes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->groupTypes = $groupTypes;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

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

    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }
}

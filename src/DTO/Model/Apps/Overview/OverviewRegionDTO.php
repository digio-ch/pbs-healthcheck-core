<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Overview;

class OverviewRegionDTO
{
    private ?string $name;

    /**
     * @var OverviewDepartmentDTO[] $children
     */
    private array $children;

    /**
     * @param string|null $name
     * @param OverviewDepartmentDTO[] $children
     */
    public function __construct(string $name = null, array $children = [])
    {
        $this->name = $name;
        $this->children = $children;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return OverviewDepartmentDTO[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }
}

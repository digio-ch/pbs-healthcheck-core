<?php

namespace App\DTO\Model\Apps\Overview;

class OverviewRegionDTO
{
    /**
     * @var string|null
     */
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

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return void
     */
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

    /**
     * @param array $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }
}

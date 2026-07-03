<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Widgets\RoleOverview;

class RoleOverviewDTO
{
    /**
     * @var ?string[]
     */
    private ?array $filter;

    /**
     * @var RoleOccupationWrapper[]
     */
    private array $data = [];

    /**
     * @param string[] $filter
     */
    public function __construct(?array $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return string[]|null
     */
    public function getFilter(): ?array
    {
        return $this->filter;
    }

    /**
     * @param string[]|null $filter
     */
    public function setFilter(?array $filter): void
    {
        $this->filter = $filter;
    }



    /**
     * @return RoleOccupationWrapper[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(RoleOccupationWrapper $roleOccupationWrapper): void
    {
        $this->data[] = $roleOccupationWrapper;
    }
}

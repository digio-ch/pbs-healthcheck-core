<?php

namespace App\DTO\Model\Apps\Widgets\RoleOverview;

class RoleOverviewDTO
{
    /**
     * @var ?string[]
     */
    private $filter;

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
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(RoleOccupationWrapper $roleOccupationWrapper): void
    {
        $this->data[] = $roleOccupationWrapper;
    }
}

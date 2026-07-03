<?php

namespace App\DTO\Model\Apps\Census;

class CensusFilterDTO
{
    /**
     * @var string[]
     */
    private ?array $roles;

    /**
     * @var int[]
     */
    private ?array $groups;

    private bool $filterMales = false;

    private bool $filterFemales = false;

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function setGroups(?array $groups): void
    {
        $this->groups = $groups;
    }


    public function isFilterMales(): bool
    {
        return $this->filterMales;
    }

    public function setFilterMales(bool $filterMales): void
    {
        $this->filterMales = $filterMales;
    }

    public function isFilterFemales(): bool
    {
        return $this->filterFemales;
    }

    public function setFilterFemales(bool $filterFemales): void
    {
        $this->filterFemales = $filterFemales;
    }
}

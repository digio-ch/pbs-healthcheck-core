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

    /**
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param array|null $roles
     */
    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return array|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

    /**
     * @param array|null $groups
     */
    public function setGroups(?array $groups): void
    {
        $this->groups = $groups;
    }


    /**
     * @return bool
     */
    public function isFilterMales(): bool
    {
        return $this->filterMales;
    }

    /**
     * @param bool $filterMales
     */
    public function setFilterMales(bool $filterMales): void
    {
        $this->filterMales = $filterMales;
    }

    /**
     * @return bool
     */
    public function isFilterFemales(): bool
    {
        return $this->filterFemales;
    }

    /**
     * @param bool $filterFemales
     */
    public function setFilterFemales(bool $filterFemales): void
    {
        $this->filterFemales = $filterFemales;
    }
}

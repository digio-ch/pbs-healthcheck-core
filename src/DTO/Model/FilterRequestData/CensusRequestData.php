<?php

namespace App\DTO\Model\FilterRequestData;

class CensusRequestData
{
    private Group $group;

    /**
     * @var string[]
     */
    private array $roles;

    /**
     * @var int[]
     */
    private array $groups;

    private bool $filterMales = false;

    private bool $filterFemales = false;

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups): void
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

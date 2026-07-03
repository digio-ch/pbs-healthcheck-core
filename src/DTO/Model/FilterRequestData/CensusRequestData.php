<?php

namespace App\DTO\Model\FilterRequestData;

use App\Entity\Midata\Group;

class CensusRequestData
{
    private Group $group;

    /**
     * @var string[]
     */
    private ?array $roles;

    /**
     * @var int[]
     */
    private ?array $groups;

    private ?bool $filterMales = false;

    private ?bool $filterFemales = false;

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

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

    /**
     * Returns the actual value  of the ?bool
     */
    public function getFilterMales(): ?bool
    {
        return $this->filterMales;
    }

    /**
     * Returns a bool and false if null
     */
    public function isFilterMales(): bool
    {
        return !!$this->filterMales;
    }

    /**
     * @param bool $filterMales
     */
    public function setFilterMales(?bool $filterMales): void
    {
        $this->filterMales = $filterMales;
    }

    /**
     * Returns the actual value  of the ?bool
     */
    public function getFilterFemales(): ?bool
    {
        return $this->filterFemales;
    }

    /**
     * Returns a bool and false if null
     */
    public function isFilterFemales(): bool
    {
        return !!$this->filterFemales;
    }

    /**
     * @param bool $filterFemales
     */
    public function setFilterFemales(?bool $filterFemales): void
    {
        $this->filterFemales = $filterFemales;
    }

    public function isEmpty(): bool
    {
        if (!is_null($this->roles)) {
            return false;
        }
        if (!is_null($this->groups)) {
            return false;
        }
        if (!is_null($this->filterMales)) {
            return false;
        }
        if (!is_null($this->filterFemales)) {
            return false;
        }

        return true;
    }
}

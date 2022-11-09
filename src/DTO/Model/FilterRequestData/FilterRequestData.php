<?php

namespace App\DTO\Model\FilterRequestData;

use App\Entity\Midata\Group;

class FilterRequestData
{
    /**
     * @var Group
     */
    protected $group;

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
}

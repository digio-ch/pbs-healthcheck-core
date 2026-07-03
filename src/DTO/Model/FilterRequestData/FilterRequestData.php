<?php

namespace App\DTO\Model\FilterRequestData;

use App\Entity\Midata\Group;

class FilterRequestData
{
    /**
     * @var Group
     */
    protected $group;

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }
}

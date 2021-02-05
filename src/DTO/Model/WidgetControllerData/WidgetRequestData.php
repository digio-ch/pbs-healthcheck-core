<?php

namespace App\DTO\Model\WidgetControllerData;

use App\Entity\Group;

class WidgetRequestData
{
    /**
     * @var Group
     */
    protected $group;

    /**
     * @var array|string[]
     */
    protected $groupTypes;

    /**
     * @var array|string[]
     */
    protected $peopleTypes;

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
     * @return array|string[]
     */
    public function getGroupTypes()
    {
        return $this->groupTypes;
    }

    /**
     * @param array|string[] $groupTypes
     */
    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }

    /**
     * @return array|string[]
     */
    public function getPeopleTypes()
    {
        return $this->peopleTypes;
    }

    /**
     * @param array|string[] $peopleTypes
     */
    public function setPeopleTypes(array $peopleTypes): void
    {
        $this->peopleTypes = $peopleTypes;
    }
}

<?php

namespace App\DTO\Model\FilterRequestData;

use App\Entity\Midata\Group;

class WidgetRequestData extends FilterRequestData
{
    /**
     * @var array|string[]
     */
    private $groupTypes;

    /**
     * @var array|string[]
     */
    private $peopleTypes;

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

<?php

declare(strict_types=1);

namespace App\DTO\Model\FilterRequestData;

class WidgetRequestData extends FilterRequestData
{
    /**
     * @var array|string[]
     */
    private ?array $groupTypes = null;

    /**
     * @var array|string[]
     */
    private ?array $peopleTypes = null;

    /**
     * @return array|string[]
     */
    public function getGroupTypes(): ?array
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
    public function getPeopleTypes(): ?array
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

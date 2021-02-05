<?php

namespace App\DTO\Model;

class FilterDataDTO
{
    /** @var string[] */
    private $dates;
    /** @var GroupTypeDTO[] */
    private $groupTypes;

    /**
     * @return string[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * @param string[] $dates
     */
    public function setDates(array $dates): void
    {
        $this->dates = $dates;
    }

    /**
     * @return GroupTypeDTO[]
     */
    public function getGroupTypes(): array
    {
        return $this->groupTypes;
    }

    /**
     * @param GroupTypeDTO[] $groupTypes
     */
    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }
}

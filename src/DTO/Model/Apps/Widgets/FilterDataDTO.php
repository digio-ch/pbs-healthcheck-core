<?php

namespace App\DTO\Model\Apps\Widgets;

use App\DTO\Model\GroupTypeDTO;

class FilterDataDTO
{
    /** @var string[] */
    private ?array $dates = null;
    /** @var GroupTypeDTO[] */
    private ?array $groupTypes = null;

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

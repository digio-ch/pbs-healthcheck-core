<?php

namespace App\DTO\Model;

class DateFilterDataDTO
{
    /** @var array $dates */
    private array $dates;

    /**
     * @return array
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * @param array $dates
     */
    public function setDates(array $dates): void
    {
        $this->dates = $dates;
    }
}

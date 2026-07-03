<?php

namespace App\DTO\Model;

class DateFilterDataDTO
{
    /** @var mixed[] $dates */
    private array $dates;

    /**
     * @return mixed[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    public function setDates(array $dates): void
    {
        $this->dates = $dates;
    }
}

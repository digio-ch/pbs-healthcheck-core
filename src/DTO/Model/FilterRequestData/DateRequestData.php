<?php

declare(strict_types=1);

namespace App\DTO\Model\FilterRequestData;

use DateTime;

class DateRequestData extends FilterRequestData
{
    private DateTime $date;

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }
}

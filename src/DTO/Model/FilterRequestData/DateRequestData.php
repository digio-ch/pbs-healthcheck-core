<?php

namespace App\DTO\Model\FilterRequestData;

use DateTime;

class DateRequestData extends FilterRequestData
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }
}

<?php

namespace App\DTO\Model\WidgetControllerData;

use DateTime;

class DateRequestData extends WidgetRequestData
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

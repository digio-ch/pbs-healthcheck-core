<?php

namespace App\DTO\Model\WidgetControllerData;

use DateTime;

class DateRangeRequestData extends WidgetRequestData
{
    /**
     * @var DateTime
     */
    private $from;

    /**
     * @var DateTime
     */
    private $to;

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @param DateTime $from
     */
    public function setFrom(DateTime $from): void
    {
        $this->from = $from;
    }

    /**
     * @return DateTime
     */
    public function getTo(): DateTime
    {
        return $this->to;
    }

    /**
     * @param DateTime $to
     */
    public function setTo(DateTime $to): void
    {
        $this->to = $to;
    }
}

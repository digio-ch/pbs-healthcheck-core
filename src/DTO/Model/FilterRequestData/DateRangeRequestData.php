<?php

namespace App\DTO\Model\FilterRequestData;

use DateTime;

class DateRangeRequestData extends FilterRequestData
{
    private DateTime $from;

    private DateTime $to;

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function setFrom(DateTime $from): void
    {
        $this->from = $from;
    }

    public function getTo(): DateTime
    {
        return $this->to;
    }

    public function setTo(DateTime $to): void
    {
        $this->to = $to;
    }
}

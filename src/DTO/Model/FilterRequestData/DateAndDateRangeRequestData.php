<?php

declare(strict_types=1);

namespace App\DTO\Model\FilterRequestData;

use DateTime;

class DateAndDateRangeRequestData extends FilterRequestData
{
    private ?DateTime $from = null;

    private ?DateTime $to = null;

    private ?DateTime $date = null;

    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    public function setFrom(?DateTime $from): void
    {
        $this->from = $from;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to): void
    {
        $this->to = $to;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
}

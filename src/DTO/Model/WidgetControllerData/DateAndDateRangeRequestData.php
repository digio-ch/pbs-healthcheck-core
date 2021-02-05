<?php

namespace App\DTO\Model\WidgetControllerData;

use DateTime;

class DateAndDateRangeRequestData extends WidgetRequestData
{
    /**
     * @var DateTime|null
     */
    private $from;

    /**
     * @var DateTime|null
     */
    private $to;

    /**
     * @var DateTime|null
     */
    private $date;

    /**
     * @return DateTime|null
     */
    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    /**
     * @param DateTime|null $from
     */
    public function setFrom(?DateTime $from): void
    {
        $this->from = $from;
    }

    /**
     * @return DateTime|null
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param DateTime|null $to
     */
    public function setTo(?DateTime $to): void
    {
        $this->to = $to;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     */
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
}

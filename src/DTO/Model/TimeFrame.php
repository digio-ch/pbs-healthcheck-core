<?php

namespace App\Model;

use DateTimeInterface;
use Exception;

/**
 * Represents either a single date or a period
 */
class TimeFrame
{
  private ?DateTimeInterface $date;
  private ?DateTimeInterface $from;
  private ?DateTimeInterface $to;

    private function __construct(
        ?DateTimeInterface $date,
        ?DateTimeInterface $from,
        ?DateTimeInterface $to
    ) {
        $this->date = $date;
        $this->from = $from;
        $this->to = $to;
    }

    public static function fromDate(DateTimeInterface $date): TimeFrame
    {
        return new TimeFrame($date, null, null);
    }

    public static function fromPeriod(DateTimeInterface $from, DateTimeInterface $to): TimeFrame
    {
        return new TimeFrame(null, $from, $to);
    }

    public function isPeriod(): bool
    {
        return is_null($this->date);
    }

    /**
     * @throws Exception if the TimeFrame is a period
     */
    public function getDate(): DateTimeInterface
    {
        if ($this->isPeriod()) {
            throw new Exception("time frame is a period. Cannot get the date");
        }

        return $this->date;
    }

    /**
     * @throws Exception if the TimeFrame is a date
     */
    public function getPeriodStart(): DateTimeInterface
    {
        if (!$this->isPeriod()) {
            throw new Exception("time frame is a date. Cannot get period");
        }

        return $this->from;
    }

    /**
     * @throws Exception if the TimeFrame is a date
     */
    public function getPeriodEnd(): DateTimeInterface
    {
        if (!$this->isPeriod()) {
            throw new Exception("time frame is a date. Cannot get period");
        }

        return $this->to;
    }
}

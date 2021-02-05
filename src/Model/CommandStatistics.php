<?php

namespace App\Model;

class CommandStatistics
{
    /** @var float */
    private $duration;
    /** @var int */
    private $items;
    /** @var string */
    private $details;
    /** @var int */
    private $peakMemoryUsage;

    /**
     * CommandStatistics constructor.
     * @param float $duration
     * @param int $items
     * @param string $details
     * @param int $peakMemoryUsage
     */
    public function __construct(float $duration, string $details, int $items = 0, int $peakMemoryUsage = 0)
    {
        $this->duration = $duration;
        $this->items = $items;
        $this->details = $details;
        $this->peakMemoryUsage = $peakMemoryUsage;
    }

    /**
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getItems(): int
    {
        return $this->items;
    }

    /**
     * @param int $items
     */
    public function setItems(int $items): void
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails($details): void
    {
        $this->details = $details;
    }

    /**
     * @return int
     */
    public function getPeakMemoryUsage(): int
    {
        return $this->peakMemoryUsage;
    }

    /**
     * @param int $peakMemoryUsage
     */
    public function setPeakMemoryUsage(int $peakMemoryUsage): void
    {
        $this->peakMemoryUsage = $peakMemoryUsage;
    }
}

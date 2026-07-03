<?php

namespace App\Model;

class CommandStatistics
{
    private float $duration;
    private int $items;
    private string $details;
    private int $peakMemoryUsage;

    /**
     * CommandStatistics constructor.
     */
    public function __construct(float $duration, string $details, int $items = 0, int $peakMemoryUsage = 0)
    {
        $this->duration = $duration;
        $this->items = $items;
        $this->details = $details;
        $this->peakMemoryUsage = $peakMemoryUsage;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    public function getItems(): int
    {
        return $this->items;
    }

    public function setItems(int $items): void
    {
        $this->items = $items;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function setDetails(string $details): void
    {
        $this->details = $details;
    }

    public function getPeakMemoryUsage(): int
    {
        return $this->peakMemoryUsage;
    }

    public function setPeakMemoryUsage(int $peakMemoryUsage): void
    {
        $this->peakMemoryUsage = $peakMemoryUsage;
    }
}

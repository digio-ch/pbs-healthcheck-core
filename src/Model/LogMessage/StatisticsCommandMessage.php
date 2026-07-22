<?php

namespace App\Model\LogMessage;

use App\Model\CommandStatistics;
use App\Service\Logger\Messages\LogMessage;

class StatisticsCommandMessage extends LogMessage
{
    /** @var string */
    private $details;
    private float $executionTime;
    private string $peakMemoryUsage;

    public function __construct(string $name, int $code, CommandStatistics $stats)
    {
        if ($stats->getItems() > 0) {
            $message = sprintf(
                'Command \'%s\' processed %d items in %s seconds exited with code %d.',
                $name,
                $stats->getItems(),
                number_format($stats->getDuration(), 2),
                $code
            );
        }

        if ($stats->getItems() === 0) {
            $message = sprintf(
                'Command \'%s\' finished in %s seconds exited with code %d.',
                $name,
                number_format($stats->getDuration(), 2),
                $code
            );
        }

        $this->details .= $stats->getDetails();
        $this->executionTime = $stats->getDuration();
        $this->peakMemoryUsage = $stats->getPeakMemoryUsage() . ' MB';
        parent::__construct($message, 'command');
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    public function getExecutionTime(): int
    {
        return $this->executionTime;
    }

    public function getPeakMemoryUsage(): string
    {
        return $this->peakMemoryUsage;
    }
}

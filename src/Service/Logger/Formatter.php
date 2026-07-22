<?php

namespace App\Service\Logger;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

readonly class Formatter implements FormatterInterface
{
    private FormatterInterface $formatter;

    public function __construct(string $logFormat)
    {
        if ($logFormat === 'text') {
            $this->formatter = new LineFormatter();
            return;
        }

        $this->formatter = new JsonFormatter();
    }

    public function format(LogRecord $record): string
    {
        return $this->formatter->format($record);
    }

    public function formatBatch(array $records): string
    {
        return $this->formatter->formatBatch($records);
    }
}

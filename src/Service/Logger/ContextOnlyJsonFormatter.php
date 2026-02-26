<?php

namespace App\Service\Logger;

use Monolog\Formatter\FormatterInterface;

class ContextOnlyJsonFormatter implements FormatterInterface
{
    public function format(array $record): string
    {
        return json_encode($record['context'], JSON_FORCE_OBJECT);
    }

    public function formatBatch(array $records): string
    {
        $output = '';
        foreach ($records as $record) {
            $output .= $this->format($record);
        }
        return $output;
    }
}

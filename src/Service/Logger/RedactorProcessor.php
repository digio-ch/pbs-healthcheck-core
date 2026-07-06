<?php

declare(strict_types=1);

namespace App\Service\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class RedactorProcessor implements ProcessorInterface
{
    private const FORBIDDEN = [
        'x-auth-token',
        'x-subject-token',
        'password',
        'cloud_init',
        'secret',
        'token',
        'signature',
        'authorization'
    ];

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            context: $this->redact($record->context),
            extra: $this->redact($record->extra),
        );
    }

    private function redact(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        $result = [];

        foreach ($value as $key => $val) {
            $needle = is_string($key) ? strtolower($key) : $key;

            $result[$key] = is_string($needle) && in_array($needle, self::FORBIDDEN, true) ? '[REDACTED]' : self::redact($val);
        }

        return $result;
    }
}

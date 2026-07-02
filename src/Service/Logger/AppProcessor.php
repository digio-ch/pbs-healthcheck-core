<?php

declare(strict_types=1);

namespace App\Service\Logger;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class AppProcessor implements ProcessorInterface
{
    private string $executor;
    private string $executionId;

    public function __construct(
        private readonly string $source,
        private readonly string $environment,
    ) {
        $this->executor = PHP_SAPI === 'cli' ? 'command' : 'request';
        $this->executionId = bin2hex(random_bytes(12));
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            extra: array_merge($record->extra, [
                'host' => $this->source,
                'env' => $this->environment,
                'executor' => $this->executor,
                'execution_id' => $this->executionId,
            ])
        );
    }
}
<?php

declare(strict_types=1);

namespace App\Service\Logger\Messages;

use Throwable;
use Doctrine\ORM\EntityManagerInterface;

class ExceptionLogMessage extends LogMessage
{
    private string $exception;

    private readonly string $stackTrace;

    public function __construct(Throwable $thrown, ?EntityManagerInterface $em = null)
    {
        parent::__construct(sprintf('%s: %s', get_class($thrown), $thrown->getMessage()), 'exception');

        $this->exception = get_class($thrown);
        $this->stackTrace = $thrown->getTraceAsString();
    }

    public function getException(): string
    {
        return $this->exception;
    }
}

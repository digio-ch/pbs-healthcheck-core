<?php

declare(strict_types=1);

namespace App\Service\Logger\Messages;

abstract class LogMessage
{
    private string $message;

    private string $type;

    /**
     * LogMessage constructor.
     */
    public function __construct(string $message, string $type)
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}

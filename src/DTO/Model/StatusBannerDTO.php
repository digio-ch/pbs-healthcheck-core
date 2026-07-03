<?php

namespace App\DTO\Model;

class StatusBannerDTO
{
    private string $severity;

    private ?object $message;

    public function __construct(string $severity, ?object $message = null)
    {
        $this->severity = $severity;
        $this->message = $message;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
    }

    public function getMessage(): ?object
    {
        return $this->message;
    }

    public function setMessage(?object $message): void
    {
        $this->message = $message;
    }
}

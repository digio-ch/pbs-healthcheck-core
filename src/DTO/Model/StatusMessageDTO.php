<?php

namespace App\DTO\Model;

class StatusMessageDTO
{
    /**
     * @var string
     */
    private string $severity;

    /**
     * @var string|null
     */
    private ?string $message;

    /**
     * @param string $severity
     * @param string | null $message
     */
    public function __construct(string $severity, ?string $message = null)
    {
        $this->severity = $severity;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     * @return void
     */
    public function setSeverity(string $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * @return string | null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return void
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}

<?php

namespace App\DTO\Model;

class StatusBannerDTO
{
    /**
     * @var string
     */
    private string $severity;

    /**
     * @var object|null
     */
    private ?object $message;

    /**
     * @param string $severity
     * @param object|null $message
     */
    public function __construct(string $severity, ?object $message = null)
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
     * @return object|null
     */
    public function getMessage(): ?object
    {
        return $this->message;
    }

    /**
     * @param object|null $message
     * @return void
     */
    public function setMessage(?object $message): void
    {
        $this->message = $message;
    }
}

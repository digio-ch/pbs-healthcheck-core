<?php

namespace App\DTO\Model;

class StatusBannerDTO
{
    /**
     * @var string
     */
    private string $severity;

    /**
     * @var StatusMessageDTO|null
     */
    private ?StatusMessageDTO $message;

    /**
     * @param string $severity
     * @param StatusMessageDTO|null $message
     */
    public function __construct(string $severity, ?StatusMessageDTO $message = null)
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
     * @return StatusMessageDTO|null
     */
    public function getMessage(): ?StatusMessageDTO
    {
        return $this->message;
    }

    /**
     * @param StatusMessageDTO|null $message
     * @return void
     */
    public function setMessage(?StatusMessageDTO $message): void
    {
        $this->message = $message;
    }
}

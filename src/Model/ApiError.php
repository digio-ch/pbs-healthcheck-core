<?php

declare(strict_types=1);

namespace App\Model;

class ApiError
{
    private int $code;

    private string $message;

    /**
     * @param int $code
     * @param string $message
     */
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}

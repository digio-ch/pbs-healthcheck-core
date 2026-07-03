<?php

namespace App\Model;

class ApiError
{
    private ?int $code = null;

    private ?string $message = null;

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}

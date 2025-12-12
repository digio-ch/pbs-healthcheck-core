<?php

namespace App\Model;

use Throwable;

class UseCaseError extends \Error
{
    public function __construct(string $message, ?int $code, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getUCMessage(): string {
        return parent::getMessage();
    }

    public function getUCCode(): ?int {
        return parent::getCode();
    }
}
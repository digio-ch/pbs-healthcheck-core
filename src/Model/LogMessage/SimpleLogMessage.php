<?php

namespace App\Model\LogMessage;

use App\Service\Logger\Messages\LogMessage;

class SimpleLogMessage extends LogMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message, 'request');
    }
}

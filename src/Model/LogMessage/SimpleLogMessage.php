<?php

namespace App\Model\LogMessage;

use Digio\Logging\Messages\LogMessage;

class SimpleLogMessage extends LogMessage
{
    public function __construct(string $message)
    {
        parent::__construct($message, 'request');
    }
}

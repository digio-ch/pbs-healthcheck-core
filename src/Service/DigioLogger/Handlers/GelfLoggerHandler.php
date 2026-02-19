<?php

namespace App\Service\DigioLogger\Handlers;

use Gelf\Message;

interface GelfLoggerHandler
{
    public function log(Message $msg);
}
<?php

namespace App\Service\Logger\Handlers;

use Gelf\Message;

interface GelfLoggerHandler
{
    public function log(Message $msg);
}

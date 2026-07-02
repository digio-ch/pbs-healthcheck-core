<?php

declare(strict_types=1);

namespace App\Service\Logger;

use Gelf\MessageInterface as Message;
use Gelf\Transport\TransportInterface;

class NopTransporter implements TransportInterface
{
    public function send(Message $message): int
    {
        return 0;
    }

}
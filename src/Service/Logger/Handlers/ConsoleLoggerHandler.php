<?php

namespace App\Service\Logger\Handlers;

use App\Service\Logger\Handlers\GelfLoggerHandler;
use Gelf\Message;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Log\Logger;

class ConsoleLoggerHandler implements GelfLoggerHandler
{
    private LoggerInterface $logger;

    /**
     * @param string $level
     */
    public function __construct(string $level)
    {
        $this->logger = new Logger($level);
    }

    public function log(Message $msg)
    {
        $jsonMsg = json_encode($msg->toArray());
        switch ($msg->getLevel()) {
            case LogLevel::DEBUG:
                $this->logger->debug($jsonMsg);
                break;
            case LogLevel::INFO:
                $this->logger->info($jsonMsg);
                break;
            case LogLevel::WARNING:
                $this->logger->warning($jsonMsg);
                break;
            case LogLevel::CRITICAL:
                $this->logger->critical($jsonMsg);
                break;
        }
    }
}

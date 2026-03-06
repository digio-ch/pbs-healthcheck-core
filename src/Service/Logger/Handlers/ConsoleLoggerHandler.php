<?php

namespace App\Service\Logger\Handlers;

use App\Service\Logger\ContextOnlyJsonFormatter;
use App\Service\Logger\Handlers\GelfLoggerHandler;
use Gelf\Message;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

class ConsoleLoggerHandler implements GelfLoggerHandler
{
    private Logger $logger;

    /**
     * @param string $level
     */
    public function __construct(string $level)
    {
        $this->logger = new Logger('app');

        $handler = new StreamHandler('php://stdout', Logger::toMonologLevel($level));
        $handler->setFormatter(new ContextOnlyJsonFormatter());

        $this->logger->pushHandler($handler);
    }

    public function log(Message $msg)
    {
        // the first parameter is for the message, which is ignored in the ContextOnlyJsonFormatter. Therefore, it can be left empty.
        switch ($msg->getLevel()) {
            case LogLevel::DEBUG:
                $this->logger->debug(null, $msg->toArray());
                break;
            case LogLevel::INFO:
                $this->logger->info(null, $msg->toArray());
                break;
            case LogLevel::WARNING:
                $this->logger->warning(null, $msg->toArray());
                break;
            case LogLevel::CRITICAL:
                $this->logger->critical(null, $msg->toArray());
                break;
        }
    }
}

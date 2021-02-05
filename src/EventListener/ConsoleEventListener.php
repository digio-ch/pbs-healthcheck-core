<?php

namespace App\EventListener;

use App\Command\StatisticsCommand;
use App\Model\CommandStatistics;
use App\Model\LogMessage\StatisticsCommandMessage;
use Digio\Logging\GelfLogger;
use Digio\Logging\Messages\CommandStartLogMessage;
use Digio\Logging\Messages\ExceptionLogMessage;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Stopwatch\Stopwatch;

class ConsoleEventListener
{
    /** @var GelfLogger */
    private $logger;

    /** @var Stopwatch */
    private $stopwatch;

    /**
     * ConsoleEventListener constructor.
     * @param GelfLogger $logger
     * @param Stopwatch $stopwatch
     */
    public function __construct(GelfLogger $logger, Stopwatch $stopwatch)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $logMessage = new CommandStartLogMessage(
            $event->getCommand()->getName(),
            $event->getInput()->getArguments()
        );
        $this->logger->info($logMessage);
        $this->stopwatch->start($event->getCommand()->getName());
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $stopwatchEvent = $this->stopwatch->stop($event->getCommand()->getName());

        if (!$event->getCommand() instanceof StatisticsCommand) {
            return;
        }

        /** @var CommandStatistics $commandStats */
        $commandStats = $event->getCommand()->getStats();
        $commandStats->setPeakMemoryUsage(round($stopwatchEvent->getMemory() / 1000000, 2));

        $logMessage = new StatisticsCommandMessage(
            $event->getCommand()->getName(),
            $event->getExitCode(),
            $commandStats
        );

        $this->stopwatch->reset();
        $this->logger->info($logMessage);
    }

    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $this->stopwatch->stop($event->getCommand()->getName());
        $this->stopwatch->reset();
        $logMessage = new ExceptionLogMessage($event->getError());
        $this->logger->critical($logMessage);
    }
}

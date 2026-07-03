<?php

namespace App\EventListener;

use App\Command\StatisticsCommand;
use App\Model\LogMessage\StatisticsCommandMessage;
use App\Service\Logger\AppLogger;
use App\Service\Logger\Messages\CommandStartLogMessage;
use App\Service\Logger\Messages\ExceptionLogMessage;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Stopwatch\Stopwatch;

class ConsoleEventListener
{
    private AppLogger $logger;

    private Stopwatch $stopwatch;

    /**
     * ConsoleEventListener constructor.
     */
    public function __construct(AppLogger $logger, Stopwatch $stopwatch)
    {
        $this->logger = $logger;
        $this->stopwatch = $stopwatch;
    }

    #[AsEventListener(event: ConsoleEvents::COMMAND)]
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $logMessage = new CommandStartLogMessage(
            $event->getCommand()->getName(),
            $event->getInput()->getArguments()
        );
        $this->logger->info($logMessage);
        $this->stopwatch->start($event->getCommand()->getName());
    }

    #[AsEventListener(event: ConsoleEvents::TERMINATE)]
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $stopwatchEvent = $this->stopwatch->stop($event->getCommand()->getName());

        if (!$event->getCommand() instanceof StatisticsCommand) {
            return;
        }

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

    #[AsEventListener(event: ConsoleEvents::ERROR)]
    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $this->stopwatch->stop($event->getCommand()->getName());
        $this->stopwatch->reset();
        $logMessage = new ExceptionLogMessage($event->getError());
        $this->logger->critical($logMessage);
    }
}

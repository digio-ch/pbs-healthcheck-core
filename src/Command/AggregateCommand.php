<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Service\Aggregator\AggregatorRegistry;
use App\Service\Aggregator\WidgetAggregator;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AggregateCommand extends StatisticsCommand
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AggregatorRegistry
     */
    protected $aggregatorRegistry;

    /**
     * @var array
     */
    private $stats = [];

    public function __construct(
        EntityManagerInterface $em,
        AggregatorRegistry $aggregatorRegistry
    ) {
        $this->em = $em;
        $this->aggregatorRegistry = $aggregatorRegistry;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('app:aggregate-data')
            ->setDescription('Aggregate data')
            ->addArgument('specific', InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ConnectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em->getConnection()->beginTransaction();
        $sqlLoggerEm = $this->em->getConfiguration()->getSQLLogger();
        $sqlLoggerConnection = $this->em->getConnection()->getConfiguration()->getSQLLogger();
        $this->em->getConfiguration()->setSQLLogger(null);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        try {
            $output->writeln(['Start aggregation...']);
            $io = new SymfonyStyle($input, $output);

            $aggregators = $this->aggregatorRegistry->getAggregators();

            $index = 0;
            foreach ($aggregators as $aggregator) {
                $index++;
                if ($index === 9) {
                    $aggregator->aggregateWithOutput($output);
                }
            }
            return 0;

            $specific = $input->getArgument('specific');

            /** @var WidgetAggregator $aggregator */
            foreach ($aggregators as $aggregator) {
                if ($specific && $aggregator->getName() !== $specific) {
                    continue;
                }

                $start = microtime(true);
                $aggregator->aggregate();
                print_r($aggregator->getName() . ": " . (memory_get_usage() / 1000 / 1000) . "\n");
                $timeElapsed = microtime(true) - $start;
                $this->stats[] = [
                    $aggregator->getName(),
                    $timeElapsed
                ];
            }

            $io->table(['Aggregator', 'Duration (s)'], $this->stats);

            $this->em->getConnection()->commit();
            $this->em->getConfiguration()->setSQLLogger($sqlLoggerEm);
            $this->em->getConnection()->getConfiguration()->setSQLLogger($sqlLoggerConnection);
            $output->writeln(['Aggregation passed successfully']);
        } catch (Exception $e) {
            $this->em->getConnection()->rollBack();
            $this->em->getConfiguration()->setSQLLogger($sqlLoggerEm);
            $this->em->getConnection()->getConfiguration()->setSQLLogger($sqlLoggerConnection);
            $output->writeln([$e->getMessage()]);
            var_dump([$e->getTraceAsString()]);
            dd($e);
        }

        return 0;
    }

    public function getStats(): CommandStatistics
    {
        $totalDuration = 0;
        $details = '';

        foreach ($this->stats as $stat) {
            $totalDuration += $stat[1];
            $details .= $stat[0] . ' finished in ' . number_format($stat[1], 2) . ' seconds.' . "\n";
        }

        return new CommandStatistics($totalDuration, $details);
    }
}

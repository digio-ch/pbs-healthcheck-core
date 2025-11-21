<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Repository\Aggregated\AggregatedQuapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertQuestionnaireAnswersCommand extends StatisticsCommand
{
    /** @var EntityManagerInterface $em */
    private EntityManagerInterface $em;

    private AggregatedQuapRepository $quapRepository;


    private float $totalDuration = 0;
    private float $processedEntries = 0;

    public function __construct(
        EntityManagerInterface $em,
        AggregatedQuapRepository $quapRepository
    ) {
        parent::__construct();

        $this->em = $em;
        $this->quapRepository = $quapRepository;
    }

    protected function configure()
    {
        $this->setName("app:convert-quap-answers");
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);
        $output->writeln('Converting questionnaire answers...');

        $this->em->wrapInTransaction(function(EntityManagerInterface $em) use ($output) {

            $connection = $em->getConnection();

            // iterate through aggregated quap entries and convert the answers to objects
            foreach ($this->quapRepository->iterateAll() as $aggregatedQuap) {

                $json = json_encode($aggregatedQuap->getAnswers(), JSON_FORCE_OBJECT);

                $connection->executeStatement(
                    'UPDATE hc_aggregated_quap SET answers = :answers WHERE id = :id',
                    [
                        'answers' => $json,
                        'id' => $aggregatedQuap->getId(),
                    ]
                );

                // free entity from the unit of work
                $em->detach($aggregatedQuap);

                $this->processedEntries++;
            }
        });

        $output->writeln("finished converting all $this->processedEntries questionnaire answers.");
        $this->totalDuration = microtime(true) - $start;
        return 0;
    }

    public function getStats(): CommandStatistics
    {
        return new CommandStatistics(
            $this->totalDuration,
            '',
            $this->processedEntries,
        );
    }
}

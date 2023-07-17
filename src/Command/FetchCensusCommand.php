<?php

namespace App\Command;

use App\Model\CommandStatistics;
use App\Service\GroupStructureAPIService;

class FetchCensusCommand extends StatisticsCommand
{
    protected GroupStructureAPIService $apiService;

    public function __construct()
    {
        parent::__construct();
    }


    public function configure()
    {
        $this->setName('app:fetch-census')
            ->setDescription('Not implemented');
    }

    // TODO: Implement the statistics
    public function getStats(): CommandStatistics
    {
        return new CommandStatistics(0, 'Statistics not yet implemented.');
    }
}

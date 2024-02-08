<?php

namespace App\Service\Census;

use App\Repository\Midata\CensusGroupRepository;

class CensusDateProvider
{
    private CensusGroupRepository $censusGroupRepository;

    public function __construct(CensusGroupRepository $censusGroupRepository)
    {
        $this->censusGroupRepository = $censusGroupRepository;
    }

    /**
     * @return int
     * @throws \Exception
     * This funciton returns the latest year we have census data for in the database.
     * It was created so that census widgets are still usable in the period between the start of a new year until
     * the census data is updated. 
     */
    public function getLatestYear(): int
    {
        return $this->censusGroupRepository->getLatestYear();
    }

    public function getRelevantDateRange(): array
    {
        return range($this->getLatestYear() - 5, $this->getLatestYear());
    }
}

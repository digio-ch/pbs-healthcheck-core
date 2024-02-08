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

    public function getLatestYear(): int
    {
        return $this->censusGroupRepository->getLatestYear();
    }

    public function getRelevantDateRange(): array
    {
        return range($this->getLatestYear() - 5, $this->getLatestYear());
    }
}

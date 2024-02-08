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
}

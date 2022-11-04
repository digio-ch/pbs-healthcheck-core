<?php

namespace App\Service;

use App\DTO\Model\DateFilterDataDTO;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDateRepository;

class DateFilterService
{
    /** @var AggregatedDateRepository $aggregatedDateRepository */
    private AggregatedDateRepository $aggregatedDateRepository;

    public function __construct(
        AggregatedDateRepository $aggregatedDateRepository
    ) {
        $this->aggregatedDateRepository = $aggregatedDateRepository;
    }

    public function getAvailableDates(Group $group): DateFilterDataDTO
    {
        $dates = $this->aggregatedDateRepository->findDataPointDatesByGroupIds(
            [$group->getId()]
        );

        $dateStrings = [];
        foreach ($dates as $date) {
            $dateStrings[] = $date['dataPointDate']->format('Y-m-d');
        }

        $dto = new DateFilterDataDTO();
        $dto->setDates($dateStrings);

        return $dto;
    }
}

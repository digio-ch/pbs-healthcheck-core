<?php

namespace App\Service\Gamification;

use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedQuapRepository;

class QuapGamificationService
{
    private AggregatedQuapRepository $aggregatedQuapRepository;
    public function __construct(
        AggregatedQuapRepository $aggregatedQuapRepository
    )
    {
        $this->aggregatedQuapRepository = $aggregatedQuapRepository;
    }

    public function logQuapEvents(array $newAnswers, Group $group)
    {
        $aggregatedQuap = $this->aggregatedQuapRepository->findCurrentForGroup($group->getId());
        $currentAnswers = $aggregatedQuap->getAnswers();
    }
}

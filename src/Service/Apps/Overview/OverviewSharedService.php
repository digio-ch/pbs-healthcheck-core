<?php

namespace App\Service\Apps\Overview;

use App\Repository\Overview\OverviewSharedRepository;

class OverviewSharedService
{
    /**
     * @var OverviewSharedRepository $sharedOverviewRepository
     */
    private OverviewSharedRepository $sharedOverviewRepository;

    public function __construct(
        OverviewSharedRepository $sharedOverviewRepository
    ) {
        $this->sharedOverviewRepository = $sharedOverviewRepository;
    }

    public function isShared(int $groupId): bool
    {
        $entry = $this->sharedOverviewRepository->findByGroupId($groupId);

        return !is_null($entry);
    }
}

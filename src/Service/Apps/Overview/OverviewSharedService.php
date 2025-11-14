<?php

namespace App\Service\Apps\Overview;

use App\Entity\Overview\OverviewShared;
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

    /**
     * @param int $groupId
     * @param bool $share whether the group should share the overview
     * @return void
     */
    public function shareOverview(int $groupId, bool $share)
    {
        $entry = $this->sharedOverviewRepository->findByGroupId($groupId);

        if (!$share && !is_null($entry)) {
            $this->sharedOverviewRepository->remove($entry);
            return;
        }

        if ($share && is_null($entry)) {
            $entry = new OverviewShared();
            $entry->setGroupId($groupId);
            $entry->setCreatedAt(new \DateTimeImmutable('now'));

            $this->sharedOverviewRepository->save($entry);
        }
    }
}

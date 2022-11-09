<?php

namespace App\Service\Apps\Widgets;

use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Repository\Midata\GroupRepository;

class MembersGroupPreviewService
{
    /** @var AggregatedDemographicGroupRepository $aggregatedDemographicGroupRepository */
    private AggregatedDemographicGroupRepository $aggregatedDemographicGroupRepository;

    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    public function __construct(
        AggregatedDemographicGroupRepository $aggregatedDemographicGroupRepository,
        GroupRepository $groupRepository
    ) {
        $this->aggregatedDemographicGroupRepository = $aggregatedDemographicGroupRepository;
        $this->groupRepository = $groupRepository;
    }

    public function getNewestDate(): ?\DateTimeImmutable
    {
        $aggregatedData = $this->aggregatedDemographicGroupRepository->findBy([], ['dataPointDate' => 'DESC'], 1);
        if (sizeof($aggregatedData) === 0) {
            return null;
        }

        return $aggregatedData[0]->getDataPointDate();
    }

    public function getGroupTypes(Group $group): array
    {
        $subGroups = $this->groupRepository->findAllSubGroupIdsByParentGroupId($group->getId());

        return array_map(function ($groupId): string {
            $g = $this->groupRepository->find($groupId);
            return $g->getGroupType()->getGroupType();
        }, $subGroups);
    }
}

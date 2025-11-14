<?php

namespace App\Service\Apps\Overview;

use App\DTO\Model\Apps\Overview\OverviewDepartmentsPreviewDTO;
use App\DTO\Model\Charts\PieChartDataDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Overview\OverviewShared;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Repository\Overview\OverviewSharedRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\Apps\Widgets\MembersGroupPreviewService;
use App\Service\DataProvider\WidgetDataProvider;
use Symfony\Contracts\Translation\TranslatorInterface;

class OverviewSharedService
{
    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var OverviewSharedRepository $sharedOverviewRepository
     */
    private OverviewSharedRepository $sharedOverviewRepository;

    /**
     * @var StatisticGroupRepository $statisticGroupRepository
     */
    private StatisticGroupRepository $statisticGroupRepository;

    /**
     * @var MembersGroupPreviewService $membersGroupPreviewService
     */
    private MembersGroupPreviewService $membersGroupPreviewService;

    /**
     * @var AggregatedDemographicGroupRepository $demographicGroupRepository
     */
    private AggregatedDemographicGroupRepository $demographicGroupRepository;

    public function __construct(
        TranslatorInterface $translator,
        OverviewSharedRepository $sharedOverviewRepository,
        StatisticGroupRepository $statisticGroupRepository,
        MembersGroupPreviewService $membersGroupPreviewService,
        AggregatedDemographicGroupRepository $demographicGroupRepository
    ) {
        $this->translator = $translator;
        $this->sharedOverviewRepository = $sharedOverviewRepository;
        $this->statisticGroupRepository = $statisticGroupRepository;
        $this->membersGroupPreviewService = $membersGroupPreviewService;
        $this->demographicGroupRepository = $demographicGroupRepository;
    }

    public function isShared(int $groupId): bool
    {
        $entry = $this->sharedOverviewRepository->findByGroupId($groupId);

        return !is_null($entry);
    }

    public function validateOverviewAccess(Group $parent, Group $department): bool
    {
        // validate group type of the parent group
        $parentGroupType = $parent->getGroupType()->getGroupType();
        if ($parentGroupType !== GroupType::REGION && $parentGroupType !== GroupType::CANTON) {
            return false;
        }

        // validate group type of the department
        if ($department->getGroupType()->getGroupType() !== GroupType::DEPARTMENT) {
            return false;
        }

        // check if the department is shared
        if (!$this->isShared($department->getId())) {
            return false;
        }

        // check if the parent group is the canton of the department
        if ($department->getCantonId() === $parent->getId()) {
            return true;
        }

        // validate if the department is the direct child of the parent
        $departmentParentGroup = $department->getParentGroup();
        if (!is_null($departmentParentGroup) && $departmentParentGroup->getId() === $parent->getId()) {
            return true;
        }

        return false;
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

    /**
     * @param Group $group
     * @return OverviewDepartmentsPreviewDTO
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDepartmentsPreview(Group $group): OverviewDepartmentsPreviewDTO
    {
        $date = $this->membersGroupPreviewService->getNewestDate();
        // if there is no aggregated data we have to return an empty preview
        if (is_null($date)) {
            return new OverviewDepartmentsPreviewDTO();
        }
        $date = $date->format("Y-m-d");

        $departmentIds = $this->statisticGroupRepository->findAllRelevantChildGroups($group->getId(), [GroupType::DEPARTMENT]);

        // filter out the departments that haven't shared the overview
        $departmentIds = array_filter($departmentIds, fn($id) => $this->isShared($id));
        $departmentCount = count($departmentIds);

        // return an empty preview because no department was shared
        if ($departmentCount === 0) {
            return new OverviewDepartmentsPreviewDTO();
        }

        $peopleCountPerGroupType = [
            GroupType::BIBER => 0,
            GroupType::WOELFE => 0,
            GroupType::PFADI => 0,
            GroupType::PIO => 0,
            GroupType::ABTEILUNGS_ROVER => 0,
            GroupType::PTA => 0,
            // leaders
            GroupType::DEPARTMENT => 0,
        ];

        // get the number of people by group types foreach department
        foreach ($departmentIds as $departmentId) {
            $groupTypes = $this->membersGroupPreviewService->getGroupTypes($departmentId);

            foreach ($groupTypes as $type) {
                $count = $this->demographicGroupRepository->findMembersCountForDateAndGroupType($date, $type, $departmentId);
                $peopleCountPerGroupType[$type] += $count ?? 0;
            }

            $leaderCount = $this->demographicGroupRepository->findTotalLeadersCountForDate($date, $departmentId, $groupTypes);
            $peopleCountPerGroupType[GroupType::DEPARTMENT] += $leaderCount ?? 0;
        }

        // map the group types to pie chart DTOs
        $pieCharts = [];

        foreach ($peopleCountPerGroupType as $groupType => $count) {
            // ignore group types that have no people in it
            if ($count === 0) {
                continue;
            }

            $groupTypeTranslation = $this->translator->trans("group.labels.members.$groupType");

            $pieChart = new PieChartDataDTO();
            $pieChart->setValue($count);
            $pieChart->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$groupType]);
            $pieChart->setName($groupTypeTranslation);

            $pieCharts[] = $pieChart;
        }

        return new OverviewDepartmentsPreviewDTO($departmentCount, $pieCharts);
    }
}

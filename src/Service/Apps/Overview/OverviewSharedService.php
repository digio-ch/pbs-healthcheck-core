<?php

namespace App\Service\Apps\Overview;

use App\DTO\Model\Apps\Overview\OverviewDepartmentDTO;
use App\DTO\Model\Apps\Overview\OverviewDepartmentsPreviewDTO;
use App\DTO\Model\Apps\Overview\OverviewRegionDTO;
use App\DTO\Model\Charts\PieChartDataDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Overview\OverviewShared;
use App\Model\OverviewPreview;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Overview\OverviewSharedRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\Apps\Widgets\MembersGroupPreviewService;
use App\Service\DataProvider\WidgetDataProvider;
use Doctrine\DBAL\Exception;
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
     * @var GroupRepository $groupRepository
     */
    private GroupRepository $groupRepository;

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
        GroupRepository $groupRepository,
        StatisticGroupRepository $statisticGroupRepository,
        MembersGroupPreviewService $membersGroupPreviewService,
        AggregatedDemographicGroupRepository $demographicGroupRepository
    ) {
        $this->translator = $translator;
        $this->sharedOverviewRepository = $sharedOverviewRepository;
        $this->groupRepository = $groupRepository;
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
        
        $departmentIds = $this->getSharedDepartments($parent);

        return in_array($department->getId(), $departmentIds);
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
        $date = $this->getLatestDemographicGroupAggregationDate();
        // if there is no aggregated data we have to return an empty preview
        if (is_null($date)) {
            return new OverviewDepartmentsPreviewDTO();
        }

        $departmentIds = $this->getSharedDepartments($group);
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

        // ignore group types that have no people in it
        foreach ($peopleCountPerGroupType as $groupType => $count) {
            if ($count === 0) {
                unset($peopleCountPerGroupType[$groupType]);
            }
        }

        // map the group types to pie chart DTOs
        $pieCharts = $this->mapToPieChartDTOs($peopleCountPerGroupType);

        return new OverviewDepartmentsPreviewDTO($departmentCount, $pieCharts);
    }

    /**
     * @param Group $group
     * @return OverviewRegionDTO[]
     * @throws Exception
     */
    public function getDepartments(Group $group): array
    {
        $date = $this->getLatestDemographicGroupAggregationDate();
        // if there is no aggregated data we have to return an empty preview
        if (is_null($date)) {
            return [];
        }

        $departmentIds = $this->getSharedDepartments($group);

        // query the number of people per group type across all departments
        $overviewPreviews = [];

        foreach ($departmentIds as $departmentId) {
            /** @var Group|null $department */
            $department = $this->groupRepository->findOneBy(['id' => $departmentId]);

            if (is_null($department)) {
                throw new \Exception("the group ($departmentId) cannot be null");
            }

            $groupTypes = $this->membersGroupPreviewService->getGroupTypes($departmentId);
            $groupTypesCount = [];

            foreach ($groupTypes as $type) {
                $count = $this->demographicGroupRepository->findMembersCountForDateAndGroupType($date, $type, $departmentId);
                $groupTypesCount[$type] = $count ?? 0;
            }

            $leaderCount = $this->demographicGroupRepository->findTotalLeadersCountForDate($date, $departmentId, $groupTypes);
            $groupTypesCount[GroupType::DEPARTMENT] = $leaderCount ?? 0;

            $overviewPreviews[] = new OverviewPreview($department, $groupTypesCount);
        }

        if ($group->getGroupType()->getGroupType() === GroupType::REGION) {
            // name can be empty because all departments are from the same region
            return [$this->mapToRegionOverviewDTO(null, $overviewPreviews)];
        }

        // group the overviews by their region if there is one
        /** @var array<int, OverviewPreview> $regionIdToOverviewPreviews */
        $regionIdToOverviewPreviews = [];
        /** @var OverviewPreview[] $regionlessOverviewPreviews */
        $regionlessOverviewPreviews = [];

        foreach ($overviewPreviews as $preview) {
            $parentGroup = $preview->getGroup()->getParentGroup();
            if (is_null($parentGroup) || $parentGroup->getGroupType()->getGroupType() !== GroupType::REGION) {
                $regionlessOverviewPreviews[] = $preview;
                continue;
            }

            if (!array_key_exists($parentGroup->getId(), $regionIdToOverviewPreviews)) {
                $regionIdToOverviewPreviews[$parentGroup->getId()] = [];
            }

            $regionIdToOverviewPreviews[$parentGroup->getId()][] = $preview;
        }

        $dtos = [];

        foreach ($regionIdToOverviewPreviews as $previews) {
            $regionName = $previews[0]->getGroup()->getParentGroup()->getName();

            $dtos[] = $this->mapToRegionOverviewDTO($regionName, $previews);
        }

        foreach ($regionlessOverviewPreviews as $preview) {
            $dtos[] = $this->mapToRegionOverviewDTO(null, [$preview]);
        }

        usort($dtos, fn($a, $b) => $this->sortOverviewRegionByName($a, $b));

        return $dtos;
    }

    /**
     * @param Group $group
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    private function getSharedDepartments(Group $group): array
    {
        $departmentIds = $this->statisticGroupRepository->findAllRelevantChildGroups($group->getId(), [GroupType::DEPARTMENT]);

        // filter out the departments that haven't shared the overview
        return array_filter($departmentIds, fn($id) => $this->isShared($id));
    }

    /**
     * @return string|null
     */
    private function getLatestDemographicGroupAggregationDate(): ?string
    {
        $date = $this->membersGroupPreviewService->getNewestDate();
        if (is_null($date)) {
            return null;
        }

        return $date->format("Y-m-d");
    }

    /**
     * @param array<string, int> $groupTypes
     * @return PieChartDataDTO[]
     */
    private function mapToPieChartDTOs(array $groupTypes): array
    {
        $pieCharts = [];

        foreach ($groupTypes as $groupType => $count) {
            $groupTypeTranslation = $this->translator->trans("group.labels.members.$groupType");

            $pieChart = new PieChartDataDTO();
            $pieChart->setValue($count);
            $pieChart->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$groupType]);
            $pieChart->setName($groupTypeTranslation);

            $pieCharts[] = $pieChart;
        }

        return $pieCharts;
    }

    /**
     * @param string|null $regionName
     * @param OverviewPreview[] $overviewPreviews
     * @return OverviewRegionDTO
     */
    public function mapToRegionOverviewDTO(?string $regionName, array $overviewPreviews): OverviewRegionDTO
    {
        $overviewDepartments = [];

        foreach ($overviewPreviews as $preview) {
            $pieCharts = $this->mapToPieChartDTOs($preview->getGroupTypes());

            $group = $preview->getGroup();

            $overviewDepartments[] = new OverviewDepartmentDTO(
                $group->getId(),
                $group->getName(),
                $pieCharts
            );
        }

        // sort alphabetically
        usort($overviewDepartments, fn($a, $b) => $this->sortOverviewDepartmentByName($a, $b));

        return new OverviewRegionDTO($regionName, $overviewDepartments);
    }

    /**
     * @param OverviewDepartmentDTO $a
     * @param OverviewDepartmentDTO $b
     * @return int
     */
    private function sortOverviewDepartmentByName(OverviewDepartmentDTO $a, OverviewDepartmentDTO $b): int
    {
        return strcmp($a->getName(), $b->getName());
    }


    /**
     * @param OverviewRegionDTO $a
     * @param OverviewRegionDTO $b
     * @return int
     */
    private function sortOverviewRegionByName(OverviewRegionDTO $a, OverviewRegionDTO $b): int
    {
        // compare children names if the region name is null
        if (is_null($a->getName()) && is_null($b->getName())) {
            return strcmp($a->getChildren()[0]->getName(), $b->getChildren()[0]->getName());
        }

        if (is_null($a->getName())) {
            return 1;
        }

        if (is_null($b->getName())) {
            return -1;
        }

        return strcmp($a->getName(), $b->getName());
    }
}

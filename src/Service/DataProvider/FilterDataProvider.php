<?php

namespace App\Service\DataProvider;

use App\DTO\Mapper\FilterDataMapper;
use App\DTO\Model\Apps\Widgets\FilterDataDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Repository\Aggregated\AggregatedDateRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilterDataProvider extends WidgetDataProvider
{
    private StatisticGroupRepository $statisticGroupRepository;

    private AggregatedDateRepository $widgetDateRepository;

    public function __construct(
        TranslatorInterface $translator,
        GroupRepository $groupRepository,
        StatisticGroupRepository $statisticGroupRepository,
        GroupTypeRepository $groupTypeRepository,
        AggregatedDateRepository $widgetDateRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->statisticGroupRepository = $statisticGroupRepository;
        $this->groupTypeRepository = $groupTypeRepository;
        $this->widgetDateRepository = $widgetDateRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    public function getGroupTypes(Group $group, string $locale): array
    {
        $groupTypes = $this->groupTypeRepository->findGroupTypesForParentGroups(
            [$group->getId()]
        );
        return FilterDataMapper::createGroupTypes($groupTypes, $locale);
    }

    public function getData(Group $group, string $locale): FilterDataDTO
    {
        $groupTypes = $this->groupTypeRepository->findGroupTypesForParentGroups(
            [$group->getId()]
        );
        $this->sortParentGroupTypes($groupTypes);

        $dates = $this->widgetDateRepository->findDataPointDatesByGroupIds(
            [$group->getId()]
        );

        return FilterDataMapper::createFromEntities($groupTypes, $dates, $locale);
    }

    public function getMyOrganizationData(Group $association, string $locale): FilterDataDTO
    {
        $departmentIds = $this->statisticGroupRepository->findAllRelevantChildGroups(
            $association->getId(),
            [GroupType::DEPARTMENT],
        );

        $groupTypes = $this->groupTypeRepository->findGroupTypesForParentGroups($departmentIds);

        $this->sortParentGroupTypes($groupTypes);

        $dates = $this->widgetDateRepository->findDataPointDatesByGroupIds(
            $departmentIds
        );

        return FilterDataMapper::createFromEntities($groupTypes, $dates, $locale);
    }

    /**
     * @param mixed[][] $groupTypes
     */
    private function sortParentGroupTypes(array &$groupTypes): void
    {
        usort($groupTypes, function (array $a, array $b): int {
            return $this->sortByGroupTypes($a['group_type'], $b['group_type']);
        });
    }
}

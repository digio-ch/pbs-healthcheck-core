<?php

namespace App\Service\DataProvider;

use App\DTO\Mapper\FilterDataMapper;
use App\DTO\Model\FilterDataDTO;
use App\Entity\midata\Group;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDateRepository;
use App\Service\Aggregator\WidgetAggregator;

class FilterDataProvider
{
    /** @var GroupRepository $groupRepository */
    private GroupRepository $groupRepository;

    /** @var GroupTypeRepository $groupTypeRepository */
    private GroupTypeRepository $groupTypeRepository;

    /** @var WidgetDateRepository $widgetDateRepository */
    private WidgetDateRepository $widgetDateRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        WidgetDateRepository $widgetDateRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupTypeRepository = $groupTypeRepository;
        $this->widgetDateRepository = $widgetDateRepository;
    }

    /***
     * @param Group $group
     * @param string $locale
     * @return FilterDataDTO
     */
    public function getData(Group $group, string $locale): FilterDataDTO
    {
        $groupTypes = $this->groupTypeRepository->findGroupTypesForParentGroup($group->getId());
        $this->sortParentGroupTypes($groupTypes);

        $subGroups = $this->groupRepository->getAllSubGroupsByGroupId($group->getId());
        $dates = $this->widgetDateRepository->findDataPointDatesByGroupIds(
            array_merge([$group->getId()], $subGroups)
        );

        $dateStrings = [];
        foreach ($dates as $date) {
            $dateStrings[] = $date['dataPointDate']->format('Y-m-d');
        }

        return FilterDataMapper::createFromEntities($groupTypes, $dateStrings, $locale);
    }

    private function sortParentGroupTypes(array &$groupTypes)
    {
        usort($groupTypes, function (array $a, array $b) {
            $indexA = array_search($a['group_type'], WidgetAggregator::$typeOrder);
            $indexB = array_search($b['group_type'], WidgetAggregator::$typeOrder);
            if ($indexA === $indexB) {
                return 0;
            }
            return ($indexA > $indexB) ? 1 : -1;
        });
    }
}

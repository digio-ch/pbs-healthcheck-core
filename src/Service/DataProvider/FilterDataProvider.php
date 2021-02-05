<?php

namespace App\Service\DataProvider;

use App\DTO\Mapper\FilterDataMapper;
use App\DTO\Model\FilterDataDTO;
use App\Exception\ApiException;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDemographicGroupRepository;
use App\Service\Aggregator\WidgetAggregator;

class FilterDataProvider
{
    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var GroupTypeRepository
     */
    private $groupTypeRepository;

    /**
     * @var WidgetDemographicGroupRepository
     */
    private $widgetDemographicGroupRepository;

    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        WidgetDemographicGroupRepository $widgetDemographicGroupRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupTypeRepository = $groupTypeRepository;
        $this->widgetDemographicGroupRepository = $widgetDemographicGroupRepository;
    }

    /***
     * @param int $groupId
     * @param string $locale
     * @return FilterDataDTO
     */
    public function getData(int $groupId, string $locale): FilterDataDTO
    {
        $groupTypes = $this->groupTypeRepository->findGroupTypesForParentGroup($groupId);
        $this->sortParentGroupTypes($groupTypes);
        $subGroups = $this->groupRepository->getAllSubGroupsByGroupId($groupId);
        $dates = $this->widgetDemographicGroupRepository->findDataPointDatesByGroupIds(
            array_merge([$groupId], $subGroups)
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

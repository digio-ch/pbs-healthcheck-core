<?php

namespace App\Service\DataProvider;

use App\DTO\Model\LineChartDataDTO;
use App\DTO\Model\LineChartDataPointDTO;
use App\Entity\Group;
use App\Exception\ApiException;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDemographicGroupRepository;
use DateTime;
use Doctrine\DBAL\DBALException;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersGroupDateRangeDataProvider extends WidgetDataProvider
{
    /**
     * @var WidgetDemographicGroupRepository
     */
    protected $widgetDemographicGroupRepository;

    /**
     * GroupMembersDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     * @param WidgetDemographicGroupRepository $widgetDemographicGroupRepository
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        WidgetDemographicGroupRepository $widgetDemographicGroupRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->widgetDemographicGroupRepository = $widgetDemographicGroupRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @param Group $group
     * @param string $from
     * @param string $to
     * @param array $subGroupTypes
     * @param array $peopleTypes
     * @return array
     * @throws DBALException
     * @throws Exception
     */
    public function getData(Group $group, string $from, string $to, array $subGroupTypes, array $peopleTypes)
    {
        $result = [];
        $leadersOnly = false;

        switch ($peopleTypes) {
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes) &&
                in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $result = array_merge($result, $this->getMemberData($from, $to, $subGroupTypes, $group->getId()));
                $result[] = $this->getSummedLeaderData($from, $to, $subGroupTypes, $group->getId());
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes):
                $result = $this->getLeaderData($from, $to, $subGroupTypes, $group->getId());
                $leadersOnly = true;
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $result = $this->getMemberData($from, $to, $subGroupTypes, $group->getId());
                break;
            default:
                return $result;
        }

        $this->translateGroupNames($result, $leadersOnly);

        return $result;
    }

    /**
     * @param $from
     * @param $to
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @return array|LineChartDataDTO[]
     * @throws Exception
     */
    private function getMemberData(string $from, string $to, array $subGroupTypes, int $mainGroupId): array
    {
        $items = [];
        foreach ($subGroupTypes as $groupType) {
            $data = $this->widgetDemographicGroupRepository
                ->findMembersCountForDateRangeAndGroupType($from, $to, $mainGroupId, $groupType);

            if ($data === null || count($data) === 0) {
                continue;
            }

            $items[] = $this->createLineChartDataDTOFromQueryResult($data, $groupType);
        }
        return $items;
    }

    /**
     * @param $from
     * @param $to
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @return LineChartDataDTO
     * @throws DBALException
     * @throws Exception
     */
    private function getSummedLeaderData(
        string $from,
        string $to,
        array $subGroupTypes,
        int $mainGroupId
    ): LineChartDataDTO {
        $leadersData = $this->widgetDemographicGroupRepository->findLeadersCountForDateRangeAndGroupTypes(
            $from,
            $to,
            $mainGroupId,
            $subGroupTypes
        );
        $lineChart = new LineChartDataDTO();
        $lineChart->setName('leaders');
        $lineChart->setColor($this->getLeadersColor());
        foreach ($leadersData as $data) {
            $lineChartPoint = new LineChartDataPointDTO();
            $date = new DateTime($data['data_point_date']);
            $lineChartPoint->setName($date->format('d.m.Y'));
            $lineChartPoint->setValue($data['total']);
            $lineChart->addSeries($lineChartPoint);
        }
        return $lineChart;
    }

    /**
     * @param string $from
     * @param string $to
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @return array|LineChartDataDTO[]
     * @throws Exception
     */
    private function getLeaderData(string $from, string $to, array $subGroupTypes, int $mainGroupId): array
    {
        $items = [];
        foreach ($subGroupTypes as $groupType) {
            $data = $this->widgetDemographicGroupRepository
                ->findLeadersCountForDateRangeAndGroupTypes($from, $to, $mainGroupId, [$groupType]);

            if ($data === null || count($data) === 0) {
                continue;
            }

            $items[] = $this->createLineChartDataDTOFromQueryResult($data, $groupType);
        }
        return $items;
    }

    /**
     * @param array $queryResult
     * @param string $groupType
     * @return LineChartDataDTO
     * @throws Exception
     */
    private function createLineChartDataDTOFromQueryResult(array $queryResult, string $groupType): LineChartDataDTO
    {
        $lineChart = new LineChartDataDTO();
        $lineChart->setName($groupType);
        $lineChart->setColor(self::GROUP_TYPE_COLORS[$groupType]);

        foreach ($queryResult as $item) {
            $lineChartPoint = new LineChartDataPointDTO();
            $date = new DateTime($item['data_point_date']);
            $lineChartPoint->setName($date->format('d.m.Y'));
            $lineChartPoint->setValue($item['total']);
            $lineChart->addSeries($lineChartPoint);
        }

        return $lineChart;
    }
}

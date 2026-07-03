<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Charts\BarChartBarDataDTO;
use App\DTO\Model\Charts\BarChartDataDTO;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicEnteredLeftRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use DateTime;
use Doctrine\DBAL\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersEnteredLeftDateRangeDataProvider extends WidgetDataProvider
{
    protected AggregatedDemographicEnteredLeftRepository $widgetDemographicEnteredLeftRepository;

    /**
     * MembersEnteredLeftDateRangeDataProvider constructor.
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        AggregatedDemographicEnteredLeftRepository $widgetDemographicEnteredLeftRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->widgetDemographicEnteredLeftRepository = $widgetDemographicEnteredLeftRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    public function getData(Group $group, string $from, string $to, array $subGroupTypes, array $peopleTypes): array
    {
        $result = $data = [];
        $leadersOnly = false;

        if (
            in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
            in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
        ) {
            $data = $this->prepareMembersData($from, $to, $group->getId(), $subGroupTypes);
            $data = array_merge_recursive($data, $this->prepareAdditionalLeadersData($from, $to, $group->getId(), $subGroupTypes));
        }

        if (
            in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
            !in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
        ) {
            $data = $this->prepareMembersData($from, $to, $group->getId(), $subGroupTypes);
        }

        if (
            !in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
            in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
        ) {
            $data = $this->prepareLeadersData($from, $to, $group->getId(), $subGroupTypes);
            $leadersOnly = true;
        }

        if (!$data) {
            return $result;
        }

        ksort($data);

        foreach ($data as $dataPointDate => $item) {
            if (!$item) {
                continue;
            }

            $barChartDataDTO = new BarChartDataDTO();
            $dt = new DateTime($dataPointDate);
            $barChartDataDTO->setName($dt->format('d.m.Y'));

            foreach ($item as $groupName => $cnt) {
                $barChartBarDataDTO = new BarChartBarDataDTO();
                $groupType = substr($groupName, 0, -4);
                $barChartBarDataDTO->setName($this->translateSingleName($groupType, $leadersOnly));
                $barChartBarDataDTO->setValue($cnt);
                $barChartBarDataDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$groupType]);
                $barChartDataDTO->addSeries($barChartBarDataDTO);
            }

            $result[] = $barChartDataDTO;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function prepareMembersData(string $from, string $to, int $parentGroupId, array $subGroupTypes): array
    {
        $data = [];

        foreach ($subGroupTypes as $groupType) {
            $data[$groupType] = $this->widgetDemographicEnteredLeftRepository->findNewExitMembersCount(
                $from,
                $to,
                $parentGroupId,
                $groupType
            );
        }

        return $this->transformQueryResultData($data);
    }

    /**
     * @throws Exception
     */
    private function prepareLeadersData(string $from, string $to, int $parentGroupId, array $subGroupTypes): array
    {
        $data = [];

        foreach ($subGroupTypes as $groupType) {
            $data[$groupType] = $this->widgetDemographicEnteredLeftRepository->findNewExitLeadersCount(
                $from,
                $to,
                $parentGroupId,
                [$groupType]
            );
        }

        return $this->transformQueryResultData($data);
    }

    /**
     * @throws Exception
     */
    private function prepareAdditionalLeadersData(string $from, string $to, int $parentGroupId, array $subGroupTypes): array
    {
        $data = $this->widgetDemographicEnteredLeftRepository->findNewExitLeadersCount(
            $from,
            $to,
            $parentGroupId,
            $subGroupTypes
        );

        return $this->transformQueryResultData([
            'leaders' => $data
        ]);
    }

    /**
     * Input array:
     *  [
     *      "group_type" => [
     *          [
     *              "dat_point_date" => ""
     *              "new_sum" => x
     *              "exit_sum" => x
     *          ]
     *      ], ...
     *  ]
     *
     * Output array:
     *  [
     *      "<date>" => [
     *          "group_type (+)" => X,
     *          "group_type (-)" => -X
     *      ], ...
     *  ]
     * @param mixed[][]|array<string, mixed[]> $data
     */
    private function transformQueryResultData(array $data): array
    {
        $result = [];

        if (!$data) {
            return $result;
        }

        foreach ($data as $groupType => $groupData) {
            foreach ($groupData as $item) {
                if (array_key_exists('new_sum', $item)) {
                    $result[$item['data_point_date']][sprintf('%s (+)', $groupType)] = $item['new_sum'];
                }
                if (array_key_exists('exit_sum', $item)) {
                    $result[$item['data_point_date']][sprintf('%s (-)', $groupType)] = 0 - $item['exit_sum'];
                }
            }
        }
        return $result;
    }
}

<?php

namespace App\Service\DataProvider;

use App\DTO\Model\BarChartBarDataDTO;
use App\DTO\Model\BarChartDataDTO;
use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDemographicEnteredLeftRepository;
use DateTime;
use Doctrine\DBAL\DBALException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersEnteredLeftDateRangeDataProvider extends WidgetDataProvider
{
    /**
     * @var WidgetDemographicEnteredLeftRepository
     */
    protected $widgetDemographicEnteredLeftRepository;

    /**
     * MembersEnteredLeftDateRangeDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     * @param WidgetDemographicEnteredLeftRepository $widgetDemographicEnteredLeftRepository
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        WidgetDemographicEnteredLeftRepository $widgetDemographicEnteredLeftRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->widgetDemographicEnteredLeftRepository = $widgetDemographicEnteredLeftRepository;
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
     */
    public function getData(Group $group, string $from, string $to, array $subGroupTypes, array $peopleTypes)
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
     * @param string $from
     * @param string $to
     * @param int $parentGroupId
     * @param array $subGroupTypes
     * @return array
     * @throws DBALException
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
     * @param string $from
     * @param string $to
     * @param int $parentGroupId
     * @param array $subGroupTypes
     * @return array
     * @throws DBALException
     */
    private function prepareLeadersData(string $from, string $to, int $parentGroupId, array $subGroupTypes)
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
     * @param string $from
     * @param string $to
     * @param int $parentGroupId
     * @param array $subGroupTypes
     * @return array
     * @throws DBALException
     */
    private function prepareAdditionalLeadersData(string $from, string $to, int $parentGroupId, array $subGroupTypes)
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
     * @param array $data
     * @return array
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

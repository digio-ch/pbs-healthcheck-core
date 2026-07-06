<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Charts\PieChartDataDTO;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Doctrine\DBAL\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersGroupDateDataProvider extends WidgetDataProvider
{
    protected AggregatedDemographicGroupRepository $widgetDemographicGroupRepository;

    /**
     * GroupMembersDataProvider constructor.
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        AggregatedDemographicGroupRepository $widgetDemographicGroupRepository
    ) {
        $this->widgetDemographicGroupRepository = $widgetDemographicGroupRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @param array|string[] $subGroupTypes
     * @param array|string[] $peopleTypes
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes): array
    {
        $result = [];
        $leadersOnly = false;

        switch ($peopleTypes) {
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes) &&
                in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $result = array_merge($result, $this->getMemberData($date, $group->getId(), $subGroupTypes));
                $result[] = $this->getSummedLeaderData($date, $group->getId(), $subGroupTypes);
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes):
                $result = $this->getLeaderDataForSubGroups($date, $subGroupTypes, $group->getId());
                $leadersOnly = true;
                break;
            case in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes):
                $result = $this->getMemberData($date, $group->getId(), $subGroupTypes);
                break;
            default:
                return $result;
        }

        usort($result, function (PieChartDataDTO $a, PieChartDataDTO $b): int {
            return $this->sortByGroupTypes($a->getName(), $b->getName());
        });

        $this->translateGroupNames($result, $leadersOnly);

        return $result;
    }

    /**
     * This will sum f_count and m_count for each sub group-type
     * @param $date
     * @param string[] $subGroupTypes
     * @return array|PieChartDataDTO[]
     * @throws Exception
     */
    private function getMemberData(string $date, int $mainGroupId, array $subGroupTypes): array
    {
        $items = [];
        foreach ($subGroupTypes as $type) {
            $count = $this->widgetDemographicGroupRepository->findMembersCountForDateAndGroupType($date, $type, $mainGroupId);
            $pieChartDataDTO = new PieChartDataDTO();
            $pieChartDataDTO->setName($type);
            $pieChartDataDTO->setValue($count === null ? 0 : $count);
            $pieChartDataDTO->setColor(self::GROUP_TYPE_COLORS[$type]);
            $items[] = $pieChartDataDTO;
        }
        return $items;
    }

    /**
     * This will sum f_count_leader and m_count_leader of every sub-group into a single PieChartDataDTO
     * @param $date
     * @param string[] $groupTypes
     * @throws Exception
     */
    private function getSummedLeaderData(string $date, int $mainGroupId, array $groupTypes): PieChartDataDTO
    {
        $leaderData = $this->widgetDemographicGroupRepository->findTotalLeadersCountForDate($date, $mainGroupId, $groupTypes);
        $pieChartDataDTO = new PieChartDataDTO();
        $pieChartDataDTO->setName('leaders');
        $pieChartDataDTO->setValue($leaderData === null ? 0 : $leaderData);
        $pieChartDataDTO->setColor($this->getLeadersColor());
        return $pieChartDataDTO;
    }

    /**
     * This will sum f_count_leader and m_count_leader for each sub group-type
     * @param $date
     * @param string[] $subGroupsTypes
     * @return array|PieChartDataDTO[]
     * @throws Exception
     */
    private function getLeaderDataForSubGroups(string $date, array $subGroupsTypes, int $mainGroupId): array
    {
        $items = [];
        foreach ($subGroupsTypes as $type) {
            $count = $this->widgetDemographicGroupRepository->findLeadersCountForDateAndGroupType($date, $type, $mainGroupId);
            $pieChartDataDTO = new PieChartDataDTO();
            $pieChartDataDTO->setName($type);
            $pieChartDataDTO->setValue($count === null ? 0 : $count);
            $pieChartDataDTO->setColor(self::GROUP_TYPE_COLORS[$type]);
            $items[] = $pieChartDataDTO;
        }
        return $items;
    }
}

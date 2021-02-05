<?php

namespace App\Service\DataProvider;

use App\DTO\Model\PieChartDataDTO;
use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\GroupTypeRepository;
use App\Repository\WidgetDemographicGroupRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersGroupDateDataProvider extends WidgetDataProvider
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
        $this->widgetDemographicGroupRepository = $widgetDemographicGroupRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @param Group $group
     * @param string $date
     * @param array|string[] $subGroupTypes
     * @param array|string[] $peopleTypes
     * @return array
     * @throws DBALException
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes)
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

        $this->translateGroupNames($result, $leadersOnly);

        return $result;
    }

    /**
     * This will sum f_count and m_count for each sub group-type
     * @param $date
     * @param int $mainGroupId
     * @param array $subGroupTypes
     * @return array|PieChartDataDTO[]
     * @throws DBALException
     */
    private function getMemberData($date, int $mainGroupId, array $subGroupTypes): array
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
     * @param int $mainGroupId
     * @param array $groupTypes
     * @return PieChartDataDTO
     * @throws DBALException
     */
    private function getSummedLeaderData($date, int $mainGroupId, array $groupTypes): PieChartDataDTO
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
     * @param array $subGroupsTypes
     * @param int $mainGroupId
     * @return array|PieChartDataDTO[]
     * @throws DBALException
     */
    private function getLeaderDataForSubGroups($date, array $subGroupsTypes, int $mainGroupId): array
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

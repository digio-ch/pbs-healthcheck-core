<?php

namespace App\Service\DataProvider;

use App\DTO\Model\BarChartBarDataDTO;
use App\DTO\Model\BarChartDataDTO;
use App\DTO\Model\ExcludeUnknownGenderChartDTO;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicDepartmentRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersBirthyearDateDataProvider extends WidgetDataProvider
{
    /**
     * @var AggregatedDemographicDepartmentRepository
     */
    protected $widgetDemographicDepartmentRepository;

    /**
     * MembersBirthyearDateDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     * @param AggregatedDemographicDepartmentRepository $widgetDemographicDepartmentRepository
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        AggregatedDemographicDepartmentRepository $widgetDemographicDepartmentRepository
    ) {
        $this->widgetDemographicDepartmentRepository = $widgetDemographicDepartmentRepository;
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
     * @return ExcludeUnknownGenderChartDTO
     * @throws DBALException
     */
    public function getData(Group $group, string $date, array $subGroupTypes, array $peopleTypes)
    {
        $result = $data = [];
        $leadersOnly = false;
        $unknownCount = 0;

        if (
            in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
            in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
        ) {
            $this->prepareMembersAndLeadersData($date, $subGroupTypes, $group->getId(), $data);
            $unknownCount = $this->getUnknownGenderMembersCount($date, $subGroupTypes, $group->getId());
            $unknownCount += $this->getUnknownGenderLeadersCount($date, $subGroupTypes, $group->getId());
        }

        if (
            in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
            !in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
        ) {
            $this->prepareMembersData($date, $subGroupTypes, $group->getId(), $data);
            $unknownCount = $this->getUnknownGenderMembersCount($date, $subGroupTypes, $group->getId());
        }

        if (
            !in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
            in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
        ) {
            $this->prepareLeadersData($date, $subGroupTypes, $group->getId(), $data);
            $unknownCount = $this->getUnknownGenderLeadersCount($date, $subGroupTypes, $group->getId());
            $leadersOnly = true;
        }

        $data = $this->transformQueryResultToBirthYearIndex($data);

        // sort data by birth-year DESC (reversed)
        krsort($data);

        $data = $this->addMissingYears($data);
        $this->sumPeopleOverAge($data, $date);

        foreach ($data as $year => $items) {
            $barChartDataDTO = new BarChartDataDTO();
            $barChartDataDTO->setName($year);

            foreach ($items as $groupName => $cnt) {
                $barChartBarDataDTO = new BarChartBarDataDTO();
                $barChartBarDataDTO->setValue($cnt);
                $groupTypeName = substr_replace($groupName, '', -4);
                $barChartBarDataDTO->setName($groupTypeName);
                $barChartBarDataDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$groupTypeName]);
                $barChartDataDTO->addSeries($barChartBarDataDTO);
            }
            $this->translateGroupNames($barChartDataDTO->getSeries(), $leadersOnly);
            $result[] = $barChartDataDTO;
        }

        $resultWithUnknownGender = new ExcludeUnknownGenderChartDTO();
        $resultWithUnknownGender->setUnknownGenderCount($unknownCount);
        $resultWithUnknownGender->setData($result);
        return $resultWithUnknownGender;
    }

    /**
     * @param string $date
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @param array $data
     * @throws DBALException
     */
    private function prepareMembersData(string $date, array $subGroupTypes, int $mainGroupId, array &$data)
    {
        foreach ($subGroupTypes as $groupType) {
            $data[$groupType] = $this->widgetDemographicDepartmentRepository->findMembersCountForDateAndGroupType(
                $date,
                $mainGroupId,
                [$groupType]
            );
        }
    }

    /**
     * @param string $date
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @param array $data
     * @throws DBALException
     */
    private function prepareLeadersData(string $date, array $subGroupTypes, int $mainGroupId, array &$data)
    {
        foreach ($subGroupTypes as $groupType) {
            $data[$groupType] = $this->widgetDemographicDepartmentRepository->findLeadersCountForDateAndGroupType(
                $date,
                $mainGroupId,
                [$groupType]
            );
        }
    }

    /**
     * @param string $date
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @param array $data
     * @throws DBALException
     */
    private function prepareMembersAndLeadersData(string $date, array $subGroupTypes, int $mainGroupId, array &$data)
    {
        foreach ($subGroupTypes as $groupType) {
            $data[$groupType] = $this->widgetDemographicDepartmentRepository
                ->findMembersCountForDateAndGroupType(
                    $date,
                    $mainGroupId,
                    [$groupType]
                );
        }
        $data['leaders'] = $this->widgetDemographicDepartmentRepository->findLeadersCountForDateAndGroupType(
            $date,
            $mainGroupId,
            $subGroupTypes
        );
    }

    /**
     * @param string $date
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @return int
     */
    private function getUnknownGenderMembersCount(string $date, array $subGroupTypes, int $mainGroupId): int
    {
        return $this->widgetDemographicDepartmentRepository->findUnknownGenderMemberCount(
            $date,
            $mainGroupId,
            $subGroupTypes
        )[0]['u'];
    }

    /**
     * @param string $date
     * @param array $subGroupTypes
     * @param int $mainGroupId
     * @return int
     */
    private function getUnknownGenderLeadersCount(string $date, array $subGroupTypes, int $mainGroupId): int
    {
        return $this->widgetDemographicDepartmentRepository->findUnknownGenderLeaderCount(
            $date,
            $mainGroupId,
            $subGroupTypes
        )[0]['u'];
    }

    /**
     * This creates and returns a new array from the query result that will have the birth-years as unique indexes
     * and the the sub-group (m/f) counts summed and grouped by their group-types
     * [
     *      <YEAR> => [
     *          <GROUP_TYPE M|F> => <COUNT>, ...
     *      ], ...
     * ]
     * @param array $result
     * @return array
     */
    private function transformQueryResultToBirthYearIndex(array $result)
    {
        $items = [];
        foreach ($result as $groupTypeName => $groupTypeData) {
            foreach ($groupTypeData as $groupData) {
                if (!array_key_exists($groupData['birthyear'], $items)) {
                    $items[$groupData['birthyear']][$groupTypeName . ' (M)'] = $groupData['m'];
                    $items[$groupData['birthyear']][$groupTypeName . ' (F)'] = 0 - $groupData['f'];
                    continue;
                }
                if (array_key_exists($groupTypeName . ' (M)', $items[$groupData['birthyear']])) {
                    $items[$groupData['birthyear']][$groupTypeName . ' (M)'] += $groupData['m'];
                }
                if (array_key_exists($groupTypeName . ' (F)', $items[$groupData['birthyear']])) {
                    $items[$groupData['birthyear']][$groupTypeName . ' (F)'] -= $groupData['f'];
                }
                $items[$groupData['birthyear']][$groupTypeName . ' (M)'] = $groupData['m'];
                $items[$groupData['birthyear']][$groupTypeName . ' (F)'] = 0 - $groupData['f'];
            }
        }
        return $items;
    }

    /**
     * @param array $data
     * @return array
     */
    private function addMissingYears(array $data): array
    {
        $newData = [];

        $earliestYearKey = array_key_last($data);
        $latestYearKey = array_key_first($data);
        $earliestYearDate = \DateTime::createFromFormat('Y', strval($earliestYearKey));
        $latestYearDate = \DateTime::createFromFormat('Y', strval($latestYearKey));
        $earliestYearDate->modify('-1 year');

        while ($latestYearDate->format('Y') !== $earliestYearDate->format('Y')) {
            if (array_key_exists($latestYearDate->format('Y'), $data)) {
                $newData[$latestYearDate->format('Y')] = $data[$latestYearDate->format('Y')];
                $latestYearDate->modify('-1 year');
                continue;
            }
            $newData[$latestYearDate->format('Y')] = [];
            $latestYearDate->modify('-1 year');
        }

        return $newData;
    }

    private function sumPeopleOverAge(array &$data, string $date, int $age = 25)
    {
        $targetDate = \DateTime::createFromFormat('Y-m-d', $date);
        $targetDate->modify('-' . $age . ' years');
        $summedValues = [];
        foreach ($data as $year => $values) {
            $currentDate = \DateTime::createFromFormat('Y', $year);
            if ($currentDate >= $targetDate) {
                continue;
            }
            foreach ($values as $groupTypeAndGender => $value) {
                if (!array_key_exists($groupTypeAndGender, $summedValues)) {
                    $summedValues[$groupTypeAndGender] = $value;
                    continue;
                }
                $summedValues[$groupTypeAndGender] += $value;
            }
            unset($data[$year]);
        }
        $data[$targetDate->format('Y')] = $summedValues;
    }
}

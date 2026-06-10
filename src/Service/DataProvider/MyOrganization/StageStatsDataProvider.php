<?php

namespace App\Service\DataProvider\MyOrganization;

use App\DTO\Model\Charts\LineChartDataDTO;
use App\DTO\Model\Charts\LineChartDataPointDTO;
use App\DTO\Model\Charts\PieChartDataDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Model\TimeFrame;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\DataProvider\WidgetDataProvider;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class StageStatsDataProvider extends WidgetDataProvider
{
    /**
    * @var StatisticGroupRepository $statisticGroupRepository
    */
    private StatisticGroupRepository $statisticGroupRepository;

    /**
    * @var AggregatedDemographicGroupRepository $aggregatedGenderRepository
    */
    private AggregatedDemographicGroupRepository $aggregatedGenderRepository;


    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        StatisticGroupRepository $statisticGroupRepository,
        AggregatedDemographicGroupRepository $aggregatedGenderRepository
    ) {
        $this->statisticGroupRepository = $statisticGroupRepository;
        $this->aggregatedGenderRepository = $aggregatedGenderRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @param Group $association
     * @param TimeFrame $timeframe
     * @param array $peopleTypes
     * @param array $groupTypes
     * @return PieChartDataDTO[]
     * @throws Exception
     */
    public function getData(Group $association, TimeFrame $timeframe, array $peopleTypes, array $groupTypes): array
    {
        $departmentIds = $this->statisticGroupRepository->findAllRelevantChildGroups(
            $association->getId(),
            [GroupType::DEPARTMENT],
        );

        if ($timeframe->isPeriod()) {
            return $this->getDataForPeriod(
                $departmentIds,
                $timeframe->getPeriodStart(),
                $timeframe->getPeriodEnd(),
                $peopleTypes,
                $groupTypes
            );
        }

        return $this->getDataForDate(
            $departmentIds,
            $timeframe->getDate(),
            $peopleTypes,
            $groupTypes
        );
    }

  /**
   * @param int[] $departmentIds
   * @param DateTimeInterface $date
   * @param string[] $peopleTypes
   * @param string[] $groupTypes
   * @return PieChartDataDTO[]
   */
    private function getDataForDate(array $departmentIds, DateTimeInterface $date, array $peopleTypes, array $groupTypes): array
    {
        $stages = $this->aggregatedGenderRepository->findGroupTypeTotalCountForDateOfGroups(
            $date->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        /**
         * @var array<string, int> $groupTypeCount
         */
        $groupTypeCount = [];

        // count members and leaders
        if (count($peopleTypes) === 2) {
            $groupTypeCount[self::PEOPLE_TYPE_LEADERS] = 0;

            foreach ($stages as $stage) {
                $groupTypeCount[self::PEOPLE_TYPE_LEADERS] += $stage['leaders'];

                $groupType = $stage['group_type'];
                $groupTypeCount[$groupType] = $stage['members'];
            }
        } else {
            // there has to be at least one because of middleware checks
            // count whatever people type is in the array
            $peopleType = $peopleTypes[0];

            foreach ($stages as $stage) {
                $groupType = $stage['group_type'];
                $groupTypeCount[$groupType] = $stage[$peopleType];
            }
        }

        $result = [];

        foreach ($groupTypeCount as $groupType => $count) {
            if ($count === 0) {
                continue;
            }

            $pieChartDataDTO = new PieChartDataDTO();
            $pieChartDataDTO->setName($groupType);
            $pieChartDataDTO->setValue($count);
            $pieChartDataDTO->setColor(self::GROUP_TYPE_COLORS[$groupType]);
            $result[] = $pieChartDataDTO;
        }

        $leadersOnly = count($peopleTypes) === 1 && $peopleTypes[0] === self::PEOPLE_TYPE_LEADERS;

        $this->translateGroupNames($result, $leadersOnly);

        return $result;
    }

  /**
   * @param int[] $departmentIds
   * @param DateTimeInterface $from
   * @param DateTimeInterface $to
   * @param string[] $peopleTypes
   * @param string[] $groupTypes
   * @return PieChartDataDTO[]
   */
    public function getDataForPeriod(
        array $departmentIds,
        DateTimeInterface $from,
        DateTimeInterface $to,
        array $peopleTypes,
        array $groupTypes
    ): array {
        $entries = $this->aggregatedGenderRepository->findGroupTypeTotalCountForPeriodOfGroups(
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        /**
         * @var array<string, array<string, int>> $groupTypeCount
         */
        $groupTypeCount = [];

        // count members and leaders
        if (count($peopleTypes) === 2) {
            $groupTypeCount[self::PEOPLE_TYPE_LEADERS] = [];

            foreach ($entries as $entry) {
                $groupType = $entry['group_type'];

                if (!array_key_exists($groupType, $groupTypeCount)) {
                    $groupTypeCount[$groupType] = [];
                }

                $dataPointDate = $entry['data_point_date'];

                $groupTypeCount[$groupType][$dataPointDate] = $entry['members'];
                $groupTypeCount[self::PEOPLE_TYPE_LEADERS][$dataPointDate] = $entry['leaders'];
            }
        } else {
            // there has to be at least one because of middleware checks
            // count whatever people type is in the array
            $peopleType = $peopleTypes[0];

            foreach ($entries as $entry) {
                $groupType = $entry['group_type'];

                if (!array_key_exists($groupType, $groupTypeCount)) {
                    $groupTypeCount[$groupType] = [];
                }

                $dataPointDate = $entry['data_point_date'];

                $groupTypeCount[$groupType][$dataPointDate] = $entry[$peopleType];
            }
        }

        $result = [];

        foreach ($groupTypeCount as $groupType => $series) {
            $lineChart = $this->toLineChartDataDTO($groupType, $series);

            $result[] = $lineChart;
        }

        $leadersOnly = count($peopleTypes) === 1 && $peopleTypes[0] === self::PEOPLE_TYPE_LEADERS;

        $this->translateGroupNames($result, $leadersOnly);

        $departmentCount = $this->aggregatedGenderRepository->findDepartmentTotalCountForPeriodOfGroups(
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $series = [];

        foreach ($departmentCount as $row) {
            $series[$row['data_point_date']] = $row['departments'];
        }

        // we want the department count to be identifiable by the departments key so no translating needed
        $result[] = $this->toLineChartDataDTO(self::DEPARTMENT_COUNT_KEY, $series);

        return $result;
    }

    /**
     * @param string $name
     * @param array<string, int> $series
     * @return LineChartDataDTO
     * @throws \Exception
     */
    public function toLineChartDataDTO(
        string $name,
        array $series
    ): LineChartDataDTO {
        $lineChart = new LineChartDataDTO();
        $lineChart->setName($name);
        $lineChart->setColor(self::GROUP_TYPE_COLORS[$name]);

        foreach ($series as $dataPointDate => $count) {
            $lineChartPoint = new LineChartDataPointDTO();
            $date = new DateTime($dataPointDate);
            $lineChartPoint->setName($date->format('d.m.Y'));
            $lineChartPoint->setValue($count);
            $lineChart->addSeries($lineChartPoint);
        }

        return $lineChart;
    }
}

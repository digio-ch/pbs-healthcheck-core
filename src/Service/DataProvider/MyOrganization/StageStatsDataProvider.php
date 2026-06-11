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
     * @return array<PieChartDataDTO|LineChartDataDTO>
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
    private function getDataForDate(
        array $departmentIds,
        DateTimeInterface $date,
        array $peopleTypes,
        array $groupTypes
    ): array {
        $rows = $this->aggregatedGenderRepository->findGroupTypeTotalCountForDateOfGroups(
            $date->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $groupTypeCount = $this->buildGroupTypeCount($rows, $peopleTypes);

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

        $this->translateGroupNames($result, $this->isLeadersOnly($peopleTypes));

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
        $rows = $this->aggregatedGenderRepository->findGroupTypeTotalCountForPeriodOfGroups(
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $chartPointsPerGroupType = $this->buildChartPointsPerGroupType($rows, $peopleTypes);

        $lineCharts = [];

        foreach ($chartPointsPerGroupType as $groupType => $series) {
            $lineCharts[] = $this->mapToLineChart($groupType, $series);
        }

        $this->translateGroupNames($lineCharts, $this->isLeadersOnly($peopleTypes));

        // we want the department count to be identifiable by the departments key so no translating needed
        $lineCharts[] = $this->getDepartmentLineChart($from, $to, $departmentIds, $groupTypes);

        return $lineCharts;
    }

    /**
     * @param string $groupType
     * @param array $series
     * @return LineChartDataDTO
     */
    public function mapToLineChart(
        string $groupType,
        array $series
    ): LineChartDataDTO {
        $lineChart = new LineChartDataDTO();
        $lineChart->setName($groupType);
        $lineChart->setColor(self::GROUP_TYPE_COLORS[$groupType]);
        $lineChart->addSeries(...$series);

        return $lineChart;
    }

    /**
     * @param array $rows
     * @param string[] $peopleTypes
     * @return array<string, int>
     */
    private function buildGroupTypeCount(array $rows, array $peopleTypes): array
    {
        /**
         * @var array<string, int> $groupTypeCount
         */
        $groupTypeCount = [];

        $isBothPeopleTypes = $this->isBothPeopleTypes($peopleTypes);

        if ($isBothPeopleTypes) {
            $groupTypeCount[self::PEOPLE_TYPE_LEADERS] = 0;
        }

        // defines from which field of the $row array the count is used
        // usually a group type reflects the members count except when leaders only is selected
        $peopleTypeKey = $this::PEOPLE_TYPE_MEMBERS;

        if ($this->isLeadersOnly($peopleTypes)) {
            $peopleTypeKey = $this::PEOPLE_TYPE_LEADERS;
        }

        foreach ($rows as $row) {
            $groupType = $row['group_type'];
            $groupTypeCount[$groupType] = $row[$peopleTypeKey];

            // we only need to add leaders as separate count if both types are selected
            if (!$isBothPeopleTypes) {
                continue;
            }

            $groupTypeCount[self::PEOPLE_TYPE_LEADERS] += $row[self::PEOPLE_TYPE_LEADERS];
        }

        return $groupTypeCount;
    }

    private function mapToChartPoint(string $dataPointDate, int $count): LineChartDataPointDTO
    {
        $name = DateTime::createFromFormat('Y-m-d H:i:s', $dataPointDate)->format('Y-m-d');
        $chartPoint = new LineChartDataPointDTO();
        $chartPoint->setName($name);
        $chartPoint->setValue($count);

        return $chartPoint;
    }

    /**
     * @param array $rows
     * @param string[] $peopleTypes
     * @return array
     */
    public function buildChartPointsPerGroupType(array $rows, array $peopleTypes): array
    {
        /**
         * @var array<string, LineChartDataPointDTO[]> $chartPointsPerGroupType
         */
        $chartPointsPerGroupType = [];

        $isBothPeopleTypes = $this->isBothPeopleTypes($peopleTypes);

        if ($isBothPeopleTypes) {
            $chartPointsPerGroupType[self::PEOPLE_TYPE_LEADERS] = [];
        }

        // defines from which field of the $row array the count is used
        // usually a group type reflects the members count except when leaders only is selected
        $peopleTypeKey = $this::PEOPLE_TYPE_MEMBERS;

        if ($this->isLeadersOnly($peopleTypes)) {
            $peopleTypeKey = $this::PEOPLE_TYPE_LEADERS;
        }

        foreach ($rows as $row) {
            $groupType = $row['group_type'];

            if (!array_key_exists($groupType, $chartPointsPerGroupType)) {
                $chartPointsPerGroupType[$groupType] = [];
            }

            $dataPointDate = $row['data_point_date'];

            $chartPointsPerGroupType[$groupType][] = $this->mapToChartPoint(
                $dataPointDate,
                $row[$peopleTypeKey]
            );

            if (!$isBothPeopleTypes) {
                continue;
            }

            $chartPointsPerGroupType[self::PEOPLE_TYPE_LEADERS][] = $this->mapToChartPoint(
                $dataPointDate,
                $row[self::PEOPLE_TYPE_LEADERS]
            );
        }
        return $chartPointsPerGroupType;
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param array $departmentIds
     * @param array $groupTypes
     * @return LineChartDataDTO
     */
    private function getDepartmentLineChart(
        DateTimeInterface $from,
        DateTimeInterface $to,
        array $departmentIds,
        array $groupTypes
    ): LineChartDataDTO {
        $departmentCount = $this->aggregatedGenderRepository->findDepartmentTotalCountForPeriodOfGroups(
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $series = array_map(
            fn($row) => $this->mapToChartPoint(
                $row['data_point_date'],
                $row['departments']
            ),
            $departmentCount
        );

        return $this->mapToLineChart(self::DEPARTMENT_COUNT_KEY, $series);
    }
}

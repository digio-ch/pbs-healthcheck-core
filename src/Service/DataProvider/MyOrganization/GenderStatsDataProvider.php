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

class GenderStatsDataProvider extends WidgetDataProvider
{
    /**
    * @var StatisticGroupRepository $statisticGroupRepository
    */
    private StatisticGroupRepository $statisticGroupRepository;

    /**
    * @var AggregatedDemographicGroupRepository $aggregatedGenderRepository
    */
    private AggregatedDemographicGroupRepository $aggregatedGenderRepository;

    private const GENDER_MALE = 'male';
    private const GENDER_FEMALE = 'female';
    private const GENDER_OTHER = 'unknown';

    private const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_OTHER,
    ];


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
    private function getDataForDate(array $departmentIds, DateTimeInterface $date, array $peopleTypes, array $groupTypes): array
    {
        $genderCount = $this->aggregatedGenderRepository->findGenderTotalCountForDateOfGroups(
            $date->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $total = $this->calculateGenderTotal($genderCount, $peopleTypes);

        $res = [];

        foreach (self::GENDERS as $gender) {
            $res[] = $this->mapToPieChart($gender, $total[$gender]);
        }

        return $res;
    }

    /**
     * @param int[] $departmentIds
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param string[] $peopleTypes
     * @param string[] $groupTypes
     * @return LineChartDataDTO[]
     * @throws \Exception
     */
    public function getDataForPeriod(
        array $departmentIds,
        DateTimeInterface $from,
        DateTimeInterface $to,
        array $peopleTypes,
        array $groupTypes
    ): array {
        $genderCounts = $this->aggregatedGenderRepository->findGenderTotalCountForPeriodOfGroups(
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $departmentsData = new LineChartDataDTO();
        $departmentsData->setName(self::DEPARTMENT_COUNT_KEY);
        $departmentsData->setColor('');

        /**
         * @var array<string, LineChartDataDTO> $genderCharts
         */
        $genderCharts = [];

        foreach (self::GENDERS as $gender) {
            $chart = new LineChartDataDTO();
            $chart->setName($this->translator->trans('gender.' . $gender));
            $chart->setColor('');

            $genderCharts[$gender] = $chart;
        }

        foreach ($genderCounts as $genderCount) {
            $date = (new DateTime($genderCount['data_point_date']))
            ->format('d.m.Y');

            $departmentsPoint = new LineChartDataPointDTO();
            $departmentsPoint->setName($date);
            $departmentsPoint->setValue($genderCount['departments']);
            $departmentsData->addSeries($departmentsPoint);

            $total = $this->calculateGenderTotal($genderCount, $peopleTypes);

            foreach (self::GENDERS as $gender) {
                $point = new LineChartDataPointDTO();
                $point->setName($date);
                $point->setValue($total[$gender]);

                $genderCharts[$gender]->addSeries($point);
            }
        }

        return [
        $departmentsData,
        ...array_values($genderCharts),
        ];
    }

    /**
     * @param string[] $peopleTypes
     * @param array $genderCount
     * @return array<string, int>
     */
    private function calculateGenderTotal(array $genderCount, array $peopleTypes): array
    {
        $total = [];

        foreach (self::GENDERS as $gender) {
            $total[$gender] = 0;
        }

        if (in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)) {
            foreach (self::GENDERS as $gender) {
                $total[$gender] += $genderCount[$gender . '_count_leader'];
            }
        }

        if (in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes)) {
            foreach (self::GENDERS as $gender) {
                $total[$gender] += $genderCount[$gender . '_count'];
            }
        }

        return $total;
    }

    /**
     * @param string $gender
     * @param int $total
     * @return PieChartDataDTO
     */
    private function mapToPieChart(string $gender, int $total): PieChartDataDTO
    {
        $pieChart = new PieChartDataDTO();
        $pieChart->setName($this->translator->trans('gender.' . $gender));
        $pieChart->setValue($total);
        $pieChart->setColor('');

        return $pieChart;
    }
}

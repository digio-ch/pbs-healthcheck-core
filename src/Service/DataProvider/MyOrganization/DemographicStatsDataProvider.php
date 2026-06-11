<?php

namespace App\Service\DataProvider\MyOrganization;

use App\DTO\Model\Apps\Widgets\ExcludeUnknownGenderChartDTO;
use App\DTO\Model\Charts\BarChartBarDataDTO;
use App\DTO\Model\Charts\BarChartDataDTO;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Repository\Aggregated\AggregatedDemographicDepartmentRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\DataProvider\WidgetDataProvider;
use DateTimeInterface;
use Doctrine\DBAL\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class DemographicStatsDataProvider extends WidgetDataProvider
{
    /**
    * @var StatisticGroupRepository $statisticGroupRepository
    */
    private StatisticGroupRepository $statisticGroupRepository;

    /**
    * @var AggregatedDemographicDepartmentRepository $demographicsRepository
    */
    private AggregatedDemographicDepartmentRepository $demographicsRepository;


    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        StatisticGroupRepository $statisticGroupRepository,
        AggregatedDemographicDepartmentRepository $demographicsRepository
    ) {
        $this->statisticGroupRepository = $statisticGroupRepository;
        $this->demographicsRepository = $demographicsRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @param Group $association
     * @param DateTimeInterface $date
     * @param array $peopleTypes
     * @param array $groupTypes
     * @return ExcludeUnknownGenderChartDTO
     * @throws Exception
     */
    public function getData(
        Group $association,
        DateTimeInterface $date,
        array $peopleTypes,
        array $groupTypes
    ): ExcludeUnknownGenderChartDTO
    {
        $departmentIds = $this->statisticGroupRepository->findAllRelevantChildGroups(
            $association->getId(),
            [GroupType::DEPARTMENT],
        );

        // query member and leader count per group type and birthyear
        $rows = $this->demographicsRepository->findCountForDateAndGroupType(
            $date->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $barsPerYear = $this->buildBarsPerYear($rows, $peopleTypes);

        $this->sumOldBirthyears($barsPerYear, $date);
        $this->addMissingYears($barsPerYear);

        $barCharts = $this->mapToBarChart($barsPerYear, $this->isLeadersOnly($peopleTypes));

        $unknownGenderCount = $this->getUnknownGenderCount(
            $date,
            $departmentIds,
            $groupTypes,
            $peopleTypes
        );

        $result = new ExcludeUnknownGenderChartDTO();
        $result->setUnknownGenderCount($unknownGenderCount);
        $result->setData($barCharts);

        return $result;
    }

    /**
     * @param array $rows
     * @param string[] $peopleTypes
     * @return array<int, BarChartBarDataDTO[]>
     */
    private function buildBarsPerYear(array $rows, array $peopleTypes): array
    {
        /**
         * Groups group type and gender per birthyear
         *```
         * [
         *      <birthYear> => [
         *          <groupType + gender> => <count>
         *      ]
         * ]
         *```
         * @var array<int, array<string, int>> $barsPerYear
         */
        $groupTypeAndGenderPerYear = [];

        $leadersOnly = $this->isLeadersOnly($peopleTypes);
        $isBothPeopleTypes = $this->isBothPeopleTypes($peopleTypes);

        // depending on the people type we want to count leaders instead of members
        $rowCountKeyPostFix = '';

        if ($leadersOnly) {
            $rowCountKeyPostFix = '_leader';
        }

        foreach ($rows as $row) {
            $birthYear = $row['birthyear'];

            if (!array_key_exists($birthYear, $groupTypeAndGenderPerYear)) {
                $groupTypeAndGenderPerYear[$birthYear] = [];
            }

            $groupType = $row['group_type'];

            // male numbers are positive / female numbers are negative
            /**
             * Defines what fields of the $row array count towards what group type and gender
             * @var array{
             *      'key': string, // key being used to extract the count from the row
             *      'groupType': string, // group type the count belongs to
             *      'isFemale': bool // whether the count is for females
             * }[] $countTypes
             */
            $countTypes = [
                [
                    'key' => 'm_count' . $rowCountKeyPostFix,
                    'groupType' => $groupType,
                    'isFemale' => false,
                ],
                [
                    'key' => 'f_count' . $rowCountKeyPostFix,
                    'groupType' => $groupType,
                    'isFemale' => true,
                ]
            ];

            // we only need to add leaders as separate count if both types are selected
            if ($isBothPeopleTypes) {
                $countTypes[] = [
                    'key' => 'm_count_leader',
                    'groupType' => self::PEOPLE_TYPE_LEADERS,
                    'isFemale' => false
                ];

                $countTypes[] = [
                    'key' => 'f_count_leader',
                    'groupType' => self::PEOPLE_TYPE_LEADERS,
                    'isFemale' => true
                ];
            }

            foreach ($countTypes as $countType)
            {
                $count = $row[$countType['key']];

                if ($countType['isFemale']) {
                    $count *= -1;
                }

                if ($count === 0) {
                    continue;
                }

                $groupTypeAndGenderKey = $this->mapToGroupTypeGenderKey($countType['groupType'], $count);
                $groupTypeAndGenderPerYear[$birthYear][$groupTypeAndGenderKey] = $count;
            }
        }

        /**
         * @var array<int, BarChartBarDataDTO[]> $barsPerYear
         */
        $barsPerYear = [];

        foreach ($groupTypeAndGenderPerYear as $birthYear => $groupTypeAndGenderCount)
        {
            // we don't care about birth years without people
            if (count($groupTypeAndGenderCount) === 0) {
                continue;
            }

            $barsPerYear[$birthYear] = [];

            foreach ($groupTypeAndGenderCount as $groupTypeAndGender => $count)
            {
                $barsPerYear[$birthYear][] = $this->mapKeyToBar($groupTypeAndGender, $count);
            }
        }

        return $barsPerYear;
    }

    /**
     * Sums up all bars older than the defined threshold starting from the given date
     * @param array<int, BarChartBarDataDTO[]> &$barsPerYear
     * @param DateTimeInterface $startingDate
     * @param int $threshold
     */
    private function sumOldBirthyears(array &$barsPerYear, DateTimeInterface $startingDate, int $threshold = 25)
    {
        $startingYear = intval($startingDate->format('Y'));
        $thresholdBirthYear = $startingYear - $threshold;

        /**
         * Map that stores the summed count per group type and gender.
         * We separate the gender because we want one bar per gender.
         * ```
         * [
         *      <groupType + gender> => <count>
         * ]
         * ```
         * @var array<string, int> $summedGroupTypeAndGender
         */
        $summedGroupTypeAndGender = [];

        foreach ($barsPerYear as $birthYear => $bars) {
            if ($birthYear > $thresholdBirthYear) {
                continue;
            }

            foreach ($bars as $bar) {
                $key = $this->mapToGroupTypeGenderKey($bar->getName(), $bar->getValue());

                if (!array_key_exists($key, $summedGroupTypeAndGender)) {
                    $summedGroupTypeAndGender[$key] = $bar->getValue();
                    continue;
                }

                $summedGroupTypeAndGender[$key] += $bar->getValue();
            }

            unset($barsPerYear[$birthYear]);
        }

        $barsPerYear[$thresholdBirthYear] = [];

        foreach ($summedGroupTypeAndGender as $key => $count) {
            $barsPerYear[$thresholdBirthYear][] = $this->mapKeyToBar($key, $count);
        }
    }

    /**
     * Maps the bar to a string consisting of group_type and gender:
     * ```
     * <group_type>_<m|f>
     * ```
     * @param string $groupType
     * @param int $count
     * @return string
     */
    private function mapToGroupTypeGenderKey(string $groupType, int $count): string
    {
        $gender = $count >= 0 ? 'm' : 'f';
        return $groupType . '_' . $gender;
    }

    /**
     * Maps groupType gender key and the count back to a bar DTO.
     * The key should have the following structure:
     * ```
     * <group_type>_<m|f>
     * ```
     * @param string $groupTypeGenderKey
     * @param int $count
     * @return BarChartBarDataDTO
     */
    private function mapKeyToBar(string $groupTypeGenderKey, int $count): BarChartBarDataDTO
    {
        // remove the gender
        $groupType = substr($groupTypeGenderKey, 0, -2);

        return $this->mapToBarChartBar($groupType, $count);
    }

    /**
     * Adds the missing years and sorts the array
     * @param array<int, BarChartBarDataDTO[]> &$barsPerYear
     */
    private function addMissingYears(array &$barsPerYear)
    {
        $lastYear = null;
        /**
         * @var int[] $missingYears
         */
        $missingYears = [];

        foreach ($barsPerYear as $year => $_) {
            if ($lastYear === null) {
                $lastYear = $year;
                continue;
            }

            $currentYear = $year;

            while ($lastYear !== $currentYear + 1) {
                $missingYears[] = $currentYear + 1;
                $currentYear++;
            }

            $lastYear = $year;
        }

        foreach ($missingYears as $missingYear)
        {
            $barsPerYear[$missingYear] = [];
        }

        krsort($barsPerYear);
    }

    /**
     * @param string $groupType
     * @param int $count
     * @return BarChartBarDataDTO
     */
    private function mapToBarChartBar(string $groupType, int $count): BarChartBarDataDTO
    {
        $bar = new BarChartBarDataDTO();
        $bar->setName($groupType);
        $bar->setValue($count);
        $bar->setColor(self::GROUP_TYPE_COLORS[$groupType]);

        return $bar;
    }

    /**
     * @param array<int, BarChartBarDataDTO[]> $barsPerYear
     * @param bool $leadersOnly
     * @return BarChartDataDTO[]
     */
    private function mapToBarChart(array $barsPerYear, bool $leadersOnly): array
    {
        $barCharts = [];

        foreach ($barsPerYear as $year => $bars) {
            $barChart = new BarChartDataDTO();
            $barChart->setName($year);
            $barChart->addSeries(...$bars);

            $this->translateGroupNames($barChart->getSeries(), $leadersOnly);
            $barCharts[] = $barChart;
        }

        return $barCharts;
    }

    /**
     * @param DateTimeInterface $date
     * @param int[] $departmentIds
     * @param string[] $groupTypes
     * @param string[] $peopleTypes
     * @return int
     * @throws Exception
     */
    private function getUnknownGenderCount(
        DateTimeInterface $date,
        array $departmentIds,
        array $groupTypes,
        array $peopleTypes
    ): int
    {
        $unknownGenderCount = 0;

        list($unknownMemberCount, $unknownLeaderCount) = $this->demographicsRepository->findUnknownGenderCount(
            $date->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        if (in_array(self::PEOPLE_TYPE_LEADERS, $peopleTypes)) {
            $unknownGenderCount += $unknownLeaderCount;
        }

        if (in_array(self::PEOPLE_TYPE_MEMBERS, $peopleTypes)) {
            $unknownGenderCount += $unknownMemberCount;
        }

        return $unknownGenderCount;
    }

}

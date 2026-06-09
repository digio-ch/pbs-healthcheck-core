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

    public function getData(Group $association, TimeFrame $timeframe, array $peopleTypes, array $groupTypes)
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

        $total = [
        "m" => 0,
        "f" => 0,
        "u" => 0
        ];

        if (in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)) {
            $total['m'] += $genderCount['m_count_leader'];
            $total['f'] += $genderCount['f_count_leader'];
            $total['u'] += $genderCount['u_count_leader'];
        }

        if (in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes)) {
            $total['m'] += $genderCount['m_count'];
            $total['f'] += $genderCount['f_count'];
            $total['u'] += $genderCount['u_count'];
        }

        $maleData = new PieChartDataDTO();
        $maleData->setName($this->translator->trans('gender.male'));
        $maleData->setValue($total['m']);
        $maleData->setColor('');

        $femaleData = new PieChartDataDTO();
        $femaleData->setName($this->translator->trans('gender.female'));
        $femaleData->setValue($total['f']);
        $femaleData->setColor('');

        $otherData = new PieChartDataDTO();
        $otherData->setName($this->translator->trans('gender.unknown'));
        $otherData->setValue($total['u']);
        $otherData->setColor('');

        return [
        $maleData,
        $femaleData,
        $otherData
        ];
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
        $genderCounts = $this->aggregatedGenderRepository->findGenderTotalCountForPeriodOfGroups(
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $departmentIds,
            $groupTypes
        );

        $departmentsData = new LineChartDataDTO();
        $departmentsData->setName('departments');
        $departmentsData->setColor('');

        $maleData = new LineChartDataDTO();
        $maleData->setName($this->translator->trans('gender.male'));
        $maleData->setColor('');

        $femaleData = new LineChartDataDTO();
        $femaleData->setName($this->translator->trans('gender.female'));
        $femaleData->setColor('');

        $otherData = new LineChartDataDTO();
        $otherData->setName($this->translator->trans('gender.unknown'));
        $otherData->setColor('');

        foreach ($genderCounts as $genderCount) {
            $date = (new DateTime($genderCount['data_point_date']))
            ->format('d.m.Y');

            $departmentsPoint = new LineChartDataPointDTO();
            $departmentsPoint->setName($date);

            $malePoint = new LineChartDataPointDTO();
            $malePoint->setName($date);

            $femalePoint = new LineChartDataPointDTO();
            $femalePoint->setName($date);

            $otherPoint = new LineChartDataPointDTO();
            $otherPoint->setName($date);

            $total = [
            "m" => 0,
            "f" => 0,
            "u" => 0
            ];

            if (in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)) {
                $total['m'] += $genderCount['m_count_leader'];
                $total['f'] += $genderCount['f_count_leader'];
                $total['u'] += $genderCount['u_count_leader'];
            }

            if (in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes)) {
                $total['m'] += $genderCount['m_count'];
                $total['f'] += $genderCount['f_count'];
                $total['u'] += $genderCount['u_count'];
            }

            $departmentsPoint->setValue($genderCount['departments']);
            $malePoint->setValue($total['m']);
            $femalePoint->setValue($total['f']);
            $otherPoint->setValue($total['u']);

            $departmentsData->addSeries($departmentsPoint);
            $maleData->addSeries($malePoint);
            $femaleData->addSeries($femalePoint);
            $otherData->addSeries($otherPoint);
        }

        return [
        $departmentsData,
        $maleData,
        $femaleData,
        $otherData,
        ];
    }
}

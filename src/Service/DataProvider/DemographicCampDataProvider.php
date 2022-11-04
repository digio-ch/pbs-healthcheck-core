<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Charts\BarChartBarDataDTO;
use App\DTO\Model\Charts\BarChartDataDTO;
use App\Entity\Aggregated\AggregatedDemographicCamp;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicCampGroupRepository;
use App\Repository\Aggregated\AggregatedDemographicCampRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Contracts\Translation\TranslatorInterface;

class DemographicCampDataProvider extends WidgetDataProvider
{
    /**
     * @var AggregatedDemographicCampRepository
     */
    protected $widgetDemographicCampRepository;

    /**
     * @var AggregatedDemographicCampGroupRepository
     */
    protected $demographicCampGroupRepository;

    /**
     * DemographicCampDataProvider constructor.
     * @param GroupRepository $groupRepository
     * @param GroupTypeRepository $groupTypeRepository
     * @param TranslatorInterface $translator
     * @param AggregatedDemographicCampRepository $widgetDemographicCampRepository
     * @param AggregatedDemographicCampGroupRepository $demographicCampGroupRepository
     */
    public function __construct(
        GroupRepository                          $groupRepository,
        GroupTypeRepository                      $groupTypeRepository,
        TranslatorInterface                      $translator,
        AggregatedDemographicCampRepository      $widgetDemographicCampRepository,
        AggregatedDemographicCampGroupRepository $demographicCampGroupRepository
    ) {
        $this->widgetDemographicCampRepository = $widgetDemographicCampRepository;
        $this->groupRepository = $groupRepository;
        $this->demographicCampGroupRepository = $demographicCampGroupRepository;
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
        $result = [];

        $events = $this->widgetDemographicCampRepository->getAllForPeriodAndMainGroup($from, $to, $group);
        if (!$events) {
            return $result;
        }

        /** @var AggregatedDemographicCamp $event */
        foreach ($events as $event) {
            $barChart = new BarChartDataDTO();
            $barChart->setName(
                sprintf('%s %s', $event->getCampName(), $event->getStartDate()->format('d.m.Y'))
            );

            if (
                in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
                !in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
            ) {
                $this->getMembersData($barChart, $event, $group->getId(), $subGroupTypes);
                $this->addCampToChart($result, $barChart);
                $this->translateGroupNames($barChart->getSeries());
                continue;
            }

            if (
                !in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
                in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
            ) {
                $this->getLeadersData($barChart, $event, $group->getId(), $subGroupTypes);
                $this->addCampToChart($result, $barChart);
                $this->translateGroupNames($barChart->getSeries(), true);
                continue;
            }

            if (
                in_array(WidgetDataProvider::PEOPLE_TYPE_MEMBERS, $peopleTypes) &&
                in_array(WidgetDataProvider::PEOPLE_TYPE_LEADERS, $peopleTypes)
            ) {
                $this->getMembersData($barChart, $event, $group->getId(), $subGroupTypes);
                $this->getAdditionalLeadersData($barChart, $event, $group->getId(), $subGroupTypes);
                $this->addCampToChart($result, $barChart);
                $this->translateGroupNames($barChart->getSeries());
                continue;
            }
        }

        return $result;
    }

    /**
     * @param BarChartDataDTO $barChart
     * @param AggregatedDemographicCamp $event
     * @param int $mainGroupId
     * @param array $groupTypes
     * @throws DBALException
     */
    private function getMembersData(
        BarChartDataDTO           $barChart,
        AggregatedDemographicCamp $event,
        int                       $mainGroupId,
        array                     $groupTypes
    ) {
        foreach ($groupTypes as $type) {
            $sum = $this->demographicCampGroupRepository->getMembersCountByCampAndGroupType(
                $event,
                $mainGroupId,
                $type
            );
            if ($sum === 0 || $sum === null) {
                continue;
            }
            $barChartBarDataDTO = new BarChartBarDataDTO();
            $barChartBarDataDTO->setName($type);
            $barChartBarDataDTO->setValue($sum);
            $barChartBarDataDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$type]);
            $barChart->addSeries($barChartBarDataDTO);
        }
    }

    /**
     * @param BarChartDataDTO $barChart
     * @param AggregatedDemographicCamp $event
     * @param int $mainGroupId
     * @param array $groupTypes
     * @throws DBALException
     */
    private function getLeadersData(
        BarChartDataDTO           $barChart,
        AggregatedDemographicCamp $event,
        int                       $mainGroupId,
        array                     $groupTypes
    ) {
        foreach ($groupTypes as $type) {
            $sum = $this->demographicCampGroupRepository->getLeadersCountByCampAndGroupType(
                $event,
                $mainGroupId,
                $type
            );
            if ($sum === 0 || $sum === null) {
                continue;
            }
            $barChartBarDataDTO = new BarChartBarDataDTO();
            $barChartBarDataDTO->setName($type);
            $barChartBarDataDTO->setValue($sum);
            $barChartBarDataDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS[$type]);
            $barChart->addSeries($barChartBarDataDTO);
        }
    }

    /**
     * @param BarChartDataDTO $barChart
     * @param AggregatedDemographicCamp $event
     * @param int $mainGroupId
     * @param array $groupTypes
     * @throws DBALException
     */
    private function getAdditionalLeadersData(
        BarChartDataDTO           $barChart,
        AggregatedDemographicCamp $event,
        int                       $mainGroupId,
        array                     $groupTypes
    ) {
        $leaders = $this->demographicCampGroupRepository->getAdditionalLeadersCountByCampAndGroupTypes(
            $event,
            $mainGroupId,
            $groupTypes
        );
        if ($leaders['sum'] === 0) {
            return;
        }
        $barChartBarDataDTO = new BarChartBarDataDTO();
        $barChartBarDataDTO->setName('leaders');
        $barChartBarDataDTO->setValue($leaders['sum']);
        $barChartBarDataDTO->setColor(WidgetDataProvider::GROUP_TYPE_COLORS['leaders']);
        $barChart->addSeries($barChartBarDataDTO);
    }

    private function addCampToChart(array &$results, BarChartDataDTO $barChartDataDTO)
    {
        if (count($barChartDataDTO->getSeries()) === 0) {
            return;
        }
        $results[] = $barChartDataDTO;
    }
}

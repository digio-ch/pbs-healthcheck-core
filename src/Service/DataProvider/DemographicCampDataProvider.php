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
use Doctrine\DBAL\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class DemographicCampDataProvider extends WidgetDataProvider
{
    protected AggregatedDemographicCampRepository $widgetDemographicCampRepository;

    protected AggregatedDemographicCampGroupRepository $demographicCampGroupRepository;

    /**
     * DemographicCampDataProvider constructor.
     */
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        AggregatedDemographicCampRepository $widgetDemographicCampRepository,
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

    public function getData(Group $group, string $from, string $to, array $subGroupTypes, array $peopleTypes): array
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
            }
        }

        return $result;
    }

    private function getMembersData(
        BarChartDataDTO $barChart,
        AggregatedDemographicCamp $event,
        int $mainGroupId,
        array $groupTypes
    ): void {
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

    private function getLeadersData(
        BarChartDataDTO $barChart,
        AggregatedDemographicCamp $event,
        int $mainGroupId,
        array $groupTypes
    ): void {
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
     * @throws Exception
     */
    private function getAdditionalLeadersData(
        BarChartDataDTO $barChart,
        AggregatedDemographicCamp $event,
        int $mainGroupId,
        array $groupTypes
    ): void {
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

    /**
     * @param BarChartDataDTO[] $results
     */
    private function addCampToChart(array &$results, BarChartDataDTO $barChartDataDTO): void
    {
        if (count($barChartDataDTO->getSeries()) === 0) {
            return;
        }
        $results[] = $barChartDataDTO;
    }
}

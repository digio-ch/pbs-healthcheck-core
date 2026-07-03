<?php

namespace App\Service\DataProvider\MyOrganization;

use App\DTO\Model\Apps\MyOrganization\PreviewDTO;
use App\Entity\Aggregated\AggregatedDate;
use App\Entity\Midata\Group;
use App\Model\TimeFrame;
use App\Repository\Aggregated\AggregatedDateRepository;
use App\Service\DataProvider\WidgetDataProvider;
use DateTimeInterface;
use Doctrine\DBAL\Exception;

class PreviewDataProvider
{
    private StageStatsDataProvider $stageStatsProvider;
    private DepartmentNamesDataProvider $departmentNamesProvider;

    private AggregatedDateRepository $aggregatedDateRepository;

    public function __construct(
        StageStatsDataProvider $stageStatsProvider,
        DepartmentNamesDataProvider $departmentNamesProvider,
        AggregatedDateRepository $aggregatedDateRepository
    ) {
        $this->stageStatsProvider = $stageStatsProvider;
        $this->departmentNamesProvider = $departmentNamesProvider;
        $this->aggregatedDateRepository = $aggregatedDateRepository;
    }

    /**
     * @throws Exception
     */
    public function getPreview(Group $association): PreviewDTO
    {
        $latestDate = $this->getLatestAggregatedDate($association);

        if (is_null($latestDate)) {
            return new PreviewDTO([], []);
        }

        $departments = $this->departmentNamesProvider->getDepartmentNames($association, $latestDate);

        $groupTypes = $this->stageStatsProvider->getData(
            $association,
            TimeFrame::fromDate($latestDate),
            [WidgetDataProvider::PEOPLE_TYPE_MEMBERS, WidgetDataProvider::PEOPLE_TYPE_LEADERS],
            WidgetDataProvider::RELEVANT_SUB_GROUP_TYPES
        );

        return new PreviewDTO($departments, $groupTypes);
    }

    private function getLatestAggregatedDate(Group $group): ?DateTimeInterface
    {
        /**
         * @var AggregatedDate[] $aggregatedData
         */
        $aggregatedData = $this->aggregatedDateRepository->findBy(
            ['group' => $group],
            ['dataPointDate' => 'DESC'],
            1
        );
        if (empty($aggregatedData)) {
            return null;
        }

        return $aggregatedData[0]->getDataPointDate();
    }
}

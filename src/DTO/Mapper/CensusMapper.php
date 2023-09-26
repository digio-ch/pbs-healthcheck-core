<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Census\DevelopmentWidgetDTO;
use App\DTO\Model\Apps\Census\LineChartDataDTO;
use App\DTO\Model\Apps\Census\TableDTO;
use App\Entity\Midata\CensusGroup;
use App\Entity\Statistics\StatisticGroup;
use Symfony\Component\Validator\Constraints\Date;

class CensusMapper
{
    /**
     * @param StatisticGroup $statisticGroup
     * @param CensusGroup[] $censusGroups
     * @param int[] $relevantYears
     * @return TableDTO
     */
    public static function mapToCensusTable(StatisticGroup $statisticGroup, array $censusGroups, array $relevantYears)
    {
        $dto = new TableDTO();
        $dto->setId($statisticGroup->getId());
        $dto->setName($statisticGroup->getName());
        $dto->setType($statisticGroup->getGroupType()->getGroupType());
        $parent = $statisticGroup->getParentGroup();
        $parentId = !is_null($parent) ? $parent->getId() : null;
        $dto->setParentId($parentId);

        if (sizeof($censusGroups) < 1) {
            $dto->setMissing(true);
            return $dto;
        }
        $dto->setMissing(false);

        $incomplete = false;
        $totalCounts = [];
        foreach ($relevantYears as $year) {
            $found = false;
            foreach ($censusGroups as $censusGroup) {
                if ($censusGroup->getYear() == $year) {
                    $totalCounts[] = $censusGroup->getCalculatedTotal();
                    $found = true;
                }
            }
            if (!$found) {
                $totalCounts[] = null;
                $incomplete = true;
            }
        }
        $dto->setAbsoluteMemberCounts($totalCounts);

        $improvementVsLastYear = null;
        $improvementVs3YearsAgo = null;
        $improvementVsAvg5Years = null;
        if (!is_null($totalCounts[count($totalCounts) - 1])) {
            if (!is_null($totalCounts[count($totalCounts) - 2])) {
                $improvementVsLastYear = (100 / $totalCounts[count($totalCounts) - 2]) * $totalCounts[count($totalCounts) - 1] - 100;
            }
            if (!is_null($totalCounts[count($totalCounts) - 4])) {
                $improvementVs3YearsAgo = (100 / $totalCounts[count($totalCounts) - 4]) * $totalCounts[count($totalCounts) - 1] - 100;
            }
        }
        if (!$incomplete) {
            $improvementVsAvg5Years = (100 / (($totalCounts[0] + $totalCounts[1] + $totalCounts[2] + $totalCounts[3] + $totalCounts[4]) / 5)) * $totalCounts[count($totalCounts) - 1] - 100;
        }
        $dto->setRelativeMemberCounts([$improvementVsLastYear, $improvementVs3YearsAgo, $improvementVsAvg5Years]);
        return $dto;
    }

    /**
     * @param StatisticGroup $statisticGroup
     * @param CensusGroup[] $censusGroups
     * @param int[] $relevantYears
     */
    public static function mapToLineChart(StatisticGroup $statisticGroup, array $censusGroups, array $relevantYears)
    {
        $groupData = new DevelopmentWidgetDTO();
        $absolute = [];
        $relative = [];
        $firstRelevantTotal = null;
        foreach ($censusGroups as $censusGroup) {
            if ($censusGroup->getYear() == $relevantYears[0]) {
                $firstRelevantTotal = $censusGroup->getCalculatedTotal();
            }
        }
        foreach ($relevantYears as $year) {
            $found = false;
            foreach ($censusGroups as $censusGroup) {
                if ($censusGroup->getYear() == $year) {
                    $found = true;
                    $absolute[] = $censusGroup->getCalculatedTotal();
                    $relative[] = $firstRelevantTotal ? 100 / $firstRelevantTotal * $censusGroup->getCalculatedTotal() - 100 : null;
                }
            }
            if (!$found) {
                $absolute[] = null;
                $relative[] = null;
            }
        }
        $absoluteDTO = new LineChartDataDTO();
        $relativeDTO = new LineChartDataDTO();

        $absoluteDTO->setLabel($statisticGroup->getName());
        $absoluteDTO->setData($absolute);
        $relativeDTO->setLabel($statisticGroup->getName());
        $relativeDTO->setData($relative);

        $return = new DevelopmentWidgetDTO();
        $return->setAbsolute([$absoluteDTO]);
        $return->setRelative([$relativeDTO]);
        return $return;
    }
}

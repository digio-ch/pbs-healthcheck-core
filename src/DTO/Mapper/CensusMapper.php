<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Census\DevelopmentWidgetDTO;
use App\DTO\Model\Apps\Census\LineChartDataDTO;
use App\DTO\Model\Apps\Census\TableDTO;
use App\DTO\Model\FilterRequestData\CensusRequestData;
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
    public static function mapToCensusTable(StatisticGroup $statisticGroup, array $censusGroups, array $relevantYears, CensusRequestData $censusRequestData)
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

        foreach ($censusGroups as $censusGroup) {
            self::filterCensusGroup($censusGroup, $censusRequestData);
        }

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
        if (!is_null($totalCounts[count($totalCounts) - 1]) && $totalCounts[count($totalCounts) - 1] !== 0) {
            if (!is_null($totalCounts[count($totalCounts) - 2]) && $totalCounts[count($totalCounts) - 2] !== 0) {
                $improvementVsLastYear = (100 / $totalCounts[count($totalCounts) - 2]) * $totalCounts[count($totalCounts) - 1] - 100;
            }
            if (!is_null($totalCounts[count($totalCounts) - 4]) && $totalCounts[count($totalCounts) - 4] !== 0) {
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
    public static function mapToLineChart(StatisticGroup $statisticGroup, array $censusGroups, array $relevantYears, CensusRequestData $censusRequestData)
    {
        $absolute = [];
        $relative = [];
        $firstRelevantTotal = null;
        foreach ($censusGroups as $censusGroup) {
            self::filterCensusGroup($censusGroup, $censusRequestData);
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

    public static function filterCensusGroup(CensusGroup $group, CensusRequestData $censusRequestData)
    {
        if (self::isFiltered('biber', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setPtaMCount(0);
        }
        if (self::isFiltered('biber', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setBiberFCount(0);
        }
        if (self::isFiltered('woelfe', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setWoelfeMCount(0);
        }
        if (self::isFiltered('woelfe', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setWoelfeFCount(0);
        }
        if (self::isFiltered('pfadis', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setPfadisMCount(0);
        }
        if (self::isFiltered('pfadis', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setPfadisFCount(0);
        }
        if (self::isFiltered('rover', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setRoverMCount(0);
        }
        if (self::isFiltered('rover', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setRoverFCount(0);
        }
        if (self::isFiltered('pio', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setPiosMCount(0);
        }
        if (self::isFiltered('pio', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setPiosFCount(0);
        }
        if (self::isFiltered('pta', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setPtaMCount(0);
        }
        if (self::isFiltered('pta', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setPtaFCount(0);
        }
        if (self::isFiltered('leiter', $censusRequestData->getRoles()) || !$censusRequestData->isFilterMales()) {
            $group->setLeiterMCount(0);
        }
        if (self::isFiltered('leiter', $censusRequestData->getRoles()) || !$censusRequestData->isFilterFemales()) {
            $group->setLeiterFCount(0);
        }
    }

    public static function isFiltered($needle, $haystack)
    {
        return stripos(json_encode($haystack ?? []), $needle) !== false;
    }
}

<?php

namespace App\Service\Apps\Census;

use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\Entity\Midata\CensusGroup;
use PHPUnit\Runner\Exception;

class CensusFilter
{
    /**
     * @param CensusRequestData $censusRequestData
     * @param CensusGroup[] $censusGroups
     * @return CensusGroup[]
     */
    public static function filterCensusGroups(CensusRequestData $censusRequestData, array $censusGroups): array
    {
        $filteredGroups = [];
        $groups = $censusRequestData->getGroups();
        $roles = $censusRequestData->getRoles();
        foreach ($censusGroups as $group) {
            if (array_search($group->getId(), $groups)) {
                continue;
            }
            $filteredGroup = clone $group;
            if (array_search('leiter', $roles)) {
                $filteredGroup->setLeiterMCount(0);
                $filteredGroup->setLeiterFCount(0);
            }
            if (array_search('biber', $roles)) {
                $filteredGroup->setBiberMCount(0);
                $filteredGroup->setBiberFCount(0);
            }
            if (array_search('woelfe', $roles)) {
                $filteredGroup->setWoelfeMCount(0);
                $filteredGroup->setWoelfeFCount(0);
            }
            if (array_search('pfadis', $roles)) {
                $filteredGroup->setPfadisMCount(0);
                $filteredGroup->setPfadisFCount(0);
            }
            if (array_search('rover', $roles)) {
                $filteredGroup->setRoverMCount(0);
                $filteredGroup->setRoverFCount(0);
            }
            if (array_search('pio', $roles)) {
                $filteredGroup->setPiosMCount(0);
                $filteredGroup->setPiosFCount(0);
            }
            if (array_search('pta', $roles)) {
                $filteredGroup->setPtaMCount(0);
                $filteredGroup->setPtaFCount(0);
            }
            if ($censusRequestData->isFilterMales()) {
                $filteredGroup->setLeiterMCount(0);
                $filteredGroup->setBiberMCount(0);
                $filteredGroup->setWoelfeMCount(0);
                $filteredGroup->setPfadisMCount(0);
                $filteredGroup->setRoverMCount(0);
                $filteredGroup->setPiosMCount(0);
                $filteredGroup->setPtaMCount(0);
            }
            if ($censusRequestData->isFilterFemales()) {
                $filteredGroup->setLeiterFCount(0);
                $filteredGroup->setBiberFCount(0);
                $filteredGroup->setWoelfeFCount(0);
                $filteredGroup->setPfadisFCount(0);
                $filteredGroup->setRoverFCount(0);
                $filteredGroup->setPiosFCount(0);
                $filteredGroup->setPtaFCount(0);
            }
            $filteredGroups[] = $filteredGroup;
        }
        return $filteredGroups;
    }
}

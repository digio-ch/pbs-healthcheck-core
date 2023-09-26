<?php

namespace App\Service\DataProvider;

use App\DTO\Model\Apps\Census\CensusFilterDTO;
use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\Entity\General\GroupSettings;
use App\Entity\Midata\Group;
use App\Repository\General\GroupSettingsRepository;

class CensusFilterDataProvider
{
    private GroupSettingsRepository $groupSettingsRepository;
    public function __construct(GroupSettingsRepository $groupSettingsRepository)
    {
        $this->groupSettingsRepository = $groupSettingsRepository;
    }

    public function getFilterData(Group $group): CensusFilterDTO
    {
        $groupSettings = $this->groupSettingsRepository->find($group->getId());
        return $this->mapGroupSettingsToCensusFilter($groupSettings);
    }

    private function mapGroupSettingsToCensusFilter(GroupSettings $groupSettings):CensusFilterDTO
    {
        $filterData = new CensusFilterDTO();
        $filterData->setFilterFemales($groupSettings->getCensusFilterFemales() ?? false);
        $filterData->setFilterMales($groupSettings->getCensusFilterMales() ?? false);
        $filterData->setRoles($groupSettings->getCensusRoles() ?? ['rover']);
        $filterData->setGroups($groupSettings->getCensusGroups() ?? []);
        return $filterData;
    }

    public function setFilterData(Group $group, CensusRequestData $censusRequestData): CensusFilterDTO
    {
        $groupSettings = $this->groupSettingsRepository->find($group->getId());
        $groupSettings->setCensusGroups($censusRequestData->getGroups());
        $groupSettings->setCensusRoles($censusRequestData->getRoles());
        $groupSettings->setCensusFilterFemales($censusRequestData->isFilterFemales());
        $groupSettings->setCensusFilterMales($censusRequestData->isFilterMales());
        return $this->mapGroupSettingsToCensusFilter($groupSettings);
    }
}

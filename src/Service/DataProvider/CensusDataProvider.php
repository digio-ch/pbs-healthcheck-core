<?php

namespace App\Service\DataProvider;

use App\DTO\Mapper\CensusMapper;
use App\DTO\Model\Apps\Census\DevelopmentWidgetDTO;
use App\DTO\Model\Apps\Census\MembersWidgetDTO;
use App\DTO\Model\Apps\Census\StackedBarElementDTO;
use App\DTO\Model\Apps\Census\TreemapWidgetDTO;
use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Statistics\StatisticGroup;
use App\Repository\Midata\CensusGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\Census\CensusDateProvider;
use Symfony\Contracts\Translation\TranslatorInterface;

class CensusDataProvider extends WidgetDataProvider
{
    private CensusGroupRepository $censusGroupRepository;
    private StatisticGroupRepository $statisticGroupRepository;
    private CensusDateProvider $censusDateProvider;
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        CensusGroupRepository $censusGroupRepository,
        StatisticGroupRepository $statisticGroupRepository,
        CensusDateProvider $censusDateProvider
    ) {
        $this->censusGroupRepository = $censusGroupRepository;
        $this->statisticGroupRepository = $statisticGroupRepository;
        $this->censusDateProvider = $censusDateProvider;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    /**
     * @return array<string, array<string, int|float>>
     */
    public function getPreviewData(Group $group): array
    {
        $flattenedGroups = $this->getRelevantGroups($group);
        $return = [
            'm' => [
                'leiter' => 0,
                'biber' => 0,
                'woelfe' => 0,
                'pfadis' => 0,
                'rover' => 0,
                'pio' => 0,
                'pta' => 0
            ],
            'f' => [
                'leiter' => 0,
                'biber' => 0,
                'woelfe' => 0,
                'pfadis' => 0,
                'rover' => 0,
                'pio' => 0,
                'pta' => 0
            ]
        ];
        foreach ($flattenedGroups as $group) {
            $censusGroup = $this->censusGroupRepository->findOneBy(['group_id' => $group->getId(), 'year' => $this->censusDateProvider->getLatestYear()]);
            if (!is_null($censusGroup)) {
                $return['m']['leiter'] += $censusGroup->getLeiterMCount();
                $return['m']['biber'] += $censusGroup->getBiberMCount();
                $return['m']['woelfe'] += $censusGroup->getWoelfeMCount();
                $return['m']['pfadis'] += $censusGroup->getPfadisMCount();
                $return['m']['rover'] += $censusGroup->getRoverMCount();
                $return['m']['pio'] += $censusGroup->getPiosMCount();
                $return['m']['pta'] += $censusGroup->getPtaMCount();

                $return['f']['leiter'] += $censusGroup->getLeiterFCount();
                $return['f']['biber'] += $censusGroup->getBiberFCount();
                $return['f']['woelfe'] += $censusGroup->getWoelfeFCount();
                $return['f']['pfadis'] += $censusGroup->getPfadisFCount();
                $return['f']['rover'] += $censusGroup->getRoverFCount();
                $return['f']['pio'] += $censusGroup->getPiosFCount();
                $return['f']['pta'] += $censusGroup->getPtaFCount();
            }
        }
        return $return;
    }


    /**
     * Returns a COPY of the group tree that is flattend.
     * For example the structure becomes:
     *
     * Kanton -> Region -> Abteilung
     *
     * instead of:
     *
     * Kanton -> Region -> Region -> Abteilung
     *
     * @param int[] $groups
     * @return StatisticGroup[]
     * @param int[] $groupIds
     */
    public function flattenGroupTree(array $groupIds): array
    {
        $groups = [];
        foreach ($groupIds as $groupId) {
            $groups[] = $this->statisticGroupRepository->findOneBy(['id' => $groupId]);
        }
        $flattenedGroups = [];
        foreach ($groups as $group) {
            $newGroup = $this->getNewGroupWithRelevantParent($group);
            if (!is_null($newGroup)) {
                $flattenedGroups[] = $newGroup;
            }
        }
        return $flattenedGroups;
    }

    public function getNewGroupWithRelevantParent(StatisticGroup $baseGroup): ?StatisticGroup
    {
        if ($baseGroup->getGroupType()->getGroupType() === GroupType::DEPARTMENT) {
            $clonedGroup = clone $baseGroup;
            $relevantParent = $this->findHighestRelevantRegion($clonedGroup);
            $clonedGroup->setParentGroup($relevantParent);
            return $clonedGroup;
        }
        if ($baseGroup->getGroupType()->getGroupType() === GroupType::REGION) {
            if ($baseGroup->getParentGroup()->getGroupType()->getGroupType() === GroupType::REGION) {
                return null;
            }
        }
        if ($baseGroup->getGroupType()->getGroupType() === GroupType::CANTON) {
            if ($baseGroup->getParentGroup()->getGroupType()->getGroupType() === GroupType::CANTON) {
                return null;
            }
        }
        return $baseGroup;
    }

    public function findHighestRelevantRegion(StatisticGroup $group)
    {
        $parentGroup = $group->getParentGroup();
        if (is_null($parentGroup)) {
            return $group;
        }
        if ($parentGroup->getGroupType()->getGroupType() === GroupType::REGION) {
            return $this->findHighestRelevantRegion($parentGroup);
        } else {
            if ($group->getGroupType()->getGroupType() === GroupType::DEPARTMENT && $parentGroup->getGroupType()->getGroupType() === GroupType::CANTON) {
                return $this->findHighestRelevantRegion($parentGroup);
            }
            return $group;
        }
    }


    /**
     * @param StatisticGroup[] $groups
     * @return StatisticGroup[]
     */
    public function sortGroups(array $groups): array
    {
        $regions = array_filter($groups, function (StatisticGroup $group): bool {
            return $group->getGroupType()->getGroupType() === GroupType::REGION;
        });
        $departments = array_filter($groups, function (StatisticGroup $group): bool {
            return $group->getGroupType()->getGroupType() === GroupType::DEPARTMENT;
        });
        usort($regions, function (StatisticGroup $a, StatisticGroup $b): int {
            return strcmp($a->getName(), $b->getName());
        });
        usort($departments, function (StatisticGroup $a, StatisticGroup $b): int {
            return strcmp($a->getName(), $b->getName());
        });

        $return = [];
        foreach ($regions as $region) {
            $return[] = $region;
            foreach ($departments as $department) {
                if ($department->getParentGroup()->getId() === $region->getId()) {
                    $return[] = $department;
                }
            }
        }

        foreach ($departments as $department) {
            if (!array_search($department, $return)) {
                $return[] = $department;
            }
        }
        return $return;
    }

    public function getRelevantGroups(Group $group): array
    {
        $groupIds = array_filter($this->statisticGroupRepository->findAllRelevantChildGroups($group->getId()), function (int $id) use ($group): bool {
 // We need to filter because the function also returns the group itself
            return !($id === $group->getId());
        });
        return $this->flattenGroupTree($groupIds);
    }

    /**
     * @return array<string, mixed[]>
     */
    public function getTableData(Group $group, CensusRequestData $censusRequestData): array
    {
        $flattenedGroups = $this->getRelevantGroups($group);
        $flattenedGroups = $this->sortGroups($flattenedGroups);
        $dataTransferObjects = [];
        $relevantYears = $this->censusDateProvider->getRelevantDateRange();
        foreach ($flattenedGroups as $flattenedGroup) {
            $dataTransferObjects[] = CensusMapper::mapToCensusTable($flattenedGroup, $this->censusGroupRepository->findBy(['group_id' => $flattenedGroup->getId()]), $relevantYears, $censusRequestData);
        }
        return [
            'years' => $relevantYears,
            'data' => $dataTransferObjects,
        ];
    }

    public function getDevelopmentData(Group $group, CensusRequestData $censusRequestData): DevelopmentWidgetDTO
    {
        $relevantGroups = $this->getRelevantGroups($group);
        $relevantGroups = $this->filterGroups($relevantGroups, $censusRequestData);
        $relevantGroups = $this->sortGroups($relevantGroups);

        $absolute = [];
        $relative = [];
        $relevantYears = $this->censusDateProvider->getRelevantDateRange();
        foreach ($relevantGroups as $relevantGroup) {
            $data = $this->censusGroupRepository->findBy(['group_id' => $relevantGroup->getId()]);
            if (!sizeof($data) == 0) {
                $dto = CensusMapper::mapToLineChart($relevantGroup, $data, $relevantYears, $censusRequestData);
                $absolute[] = $dto->getAbsolute()[0];
                $relative[] = $dto->getRelative()[0];
            }
        }
        $return = new DevelopmentWidgetDTO();
        $return->setYears($relevantYears);
        $return->setAbsolute($absolute);
        $return->setRelative($relative);
        return $return;
    }

    /**
     * @return array<string, int|MembersWidgetDTO[]|mixed[]>
     */
    public function getMembersData(Group $group, CensusRequestData $censusRequestData): array
    {
        $relevantGroups = $this->getRelevantGroups($group);
        $relevantGroups = $this->filterGroups($relevantGroups, $censusRequestData);
        $relevantGroups = $this->sortGroups($relevantGroups);

        $rawResults = [];
        foreach ($relevantGroups as $relevantGroup) {
            $data = $this->censusGroupRepository->findBy(['group_id' => $relevantGroup->getId(), 'year' => $this->censusDateProvider->getLatestYear()]);
            if (!sizeof($data) == 0) {
                CensusMapper::filterCensusGroup($data[0], $censusRequestData);
                $biber = $data[0]->getBiberMCount() + $data[0]->getBiberFCount();
                $woelfe = $data[0]->getWoelfeMCount() + $data[0]->getWoelfeFCount();
                $pfadi = $data[0]->getPfadisMCount() + $data[0]->getPfadisFCount();
                $pio = $data[0]->getPiosMCount() + $data[0]->getPiosFCount();
                $rover = $data[0]->getRoverMCount() + $data[0]->getRoverFCount();
                $pta = $data[0]->getPtaMCount() + $data[0]->getPtaFCount();
                $leaders = $data[0]->getLeiterMCount() + $data[0]->getLeiterFCount();
                $rawResults[0][] = new StackedBarElementDTO($biber, $data[0]->getName(), '#EEE09F');
                $rawResults[1][] = new StackedBarElementDTO($woelfe, $data[0]->getName(), '#3BB5DC');
                $rawResults[2][] = new StackedBarElementDTO($pfadi, $data[0]->getName(), '#9A7A54');
                $rawResults[4][] = new StackedBarElementDTO($rover, $data[0]->getName(), '#1DA650');
                $rawResults[3][] = new StackedBarElementDTO($pio, $data[0]->getName(), '#DD1F19');
                $rawResults[5][] = new StackedBarElementDTO($pta, $data[0]->getName(), '#d9b826');
                $rawResults[6][] = new StackedBarElementDTO($leaders, $data[0]->getName(), '#005716');
            }
        }
        $return = [];
        foreach ($rawResults as $rawResult) {
            $dto = new MembersWidgetDTO();
            $dto->setData($rawResult);
            $return[] = $dto;
        }
        return ['data' => $return, 'year' => $this->censusDateProvider->getLatestYear()];
    }

    /**
     * @return array<string, int|list<TreemapWidgetDTO>>
     */
    public function getTreemapData(Group $group, CensusRequestData $censusRequestData): array
    {
        $relevantGroups = $this->getRelevantGroups($group);
        $relevantGroups = $this->filterGroups($relevantGroups, $censusRequestData);

        $return = [];
        foreach ($relevantGroups as $relevantGroup) {
            $data = $this->censusGroupRepository->findBy(['group_id' => $relevantGroup->getId(), 'year' => $this->censusDateProvider->getLatestYear()]);
            if (!sizeof($data) == 0) {
                CensusMapper::filterCensusGroup($data[0], $censusRequestData);
                $dto = new TreemapWidgetDTO();
                $dto->setName($relevantGroup->getName());
                $parentName = $relevantGroup->getParentGroup()->getName();
                $dto->setRegion($parentName);
                $dto->setValue($data[0]->getCalculatedTotal());
                $dto->setColor(CensusMapper::getLightColorForId($relevantGroup->getParentGroup()->getId()));
                $return[] = $dto;
            }
        }
        return ['data' => $return, 'year' => $this->censusDateProvider->getLatestYear()];
    }

    /**
     * Filter out groups based on the Frontend Table filter
     * @param StatisticGroup[] $statisticGroups
     */
    private function filterGroups(array $statisticGroups, CensusRequestData $censusRequestData): array
    {
        // For faster lookups we swap array index with value so that array goes from [1 => 23, 2 => 352] to [23 => null, 352 => null]
        if (is_null($censusRequestData->getGroups())) {
            return $statisticGroups;
        }
        $groupIdsToFilterOut = array_flip($censusRequestData->getGroups());
        $filteredGroups = array_filter($statisticGroups, function (StatisticGroup $group) use ($groupIdsToFilterOut): bool {
            return !isset($groupIdsToFilterOut[$group->getId()]);
        });
        // Ensure that they are sequential.
        return array_values($filteredGroups);
    }
}

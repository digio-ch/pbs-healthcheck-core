<?php

namespace App\Service\DataProvider;

use App\DTO\Mapper\CensusMapper;
use App\DTO\Model\Apps\Census\TableDTO;
use App\DTO\Model\FilterRequestData\CensusRequestData;
use App\Entity\Midata\CensusGroup;
use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Entity\Statistics\StatisticGroup;
use App\Repository\Midata\CensusGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use App\Repository\Statistics\StatisticGroupRepository;
use App\Service\Apps\Census\CensusFilter;
use Doctrine\DBAL\Schema\Table;
use Sentry\Util\JSON;
use Symfony\Contracts\Translation\TranslatorInterface;

class CensusDataProvider extends WidgetDataProvider
{

    private CensusGroupRepository $censusGroupRepository;
    private StatisticGroupRepository $statisticGroupRepository;
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        CensusGroupRepository $censusGroupRepository,
        StatisticGroupRepository $statisticGroupRepository
    ) {
        $this->censusGroupRepository = $censusGroupRepository;
        $this->statisticGroupRepository = $statisticGroupRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    public function getPreviewData(Group $group) {
        $groups = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($group->getId(), ['Group::Abteilung', 'Group::Kantonalverband', 'Group::Region']); // Replace with group endpoint
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
        foreach ($groups as $group) {
            $censusGroup = $this->censusGroupRepository->findOneBy(['group_id' => $group['id'], 'year' => date('Y')]);
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
     */
    public function flattenGroupTree(array $groupIds) {
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

    /**
     * @param StatisticGroup $baseGroup
     * @return StatisticGroup|null
     */
    public function getNewGroupWithRelevantParent(StatisticGroup $baseGroup) {
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

    public function findHighestRelevantRegion(StatisticGroup $group) {
        $parentGroup = $group->getParentGroup();
        if (is_null($parentGroup)) {
            return $group;
        }
        if ($parentGroup->getGroupType()->getGroupType() === GroupType::REGION) {
            return $this->findHighestRelevantRegion($parentGroup);
        } else {
            return $group;
        }
    }

    /**
     * @param TableDTO[] $dtos
     * @return void
     */
    public function sortDTOs(array $dtos) {
        $regions = array_filter($dtos, function ($dto) {
            return $dto->getType() === GroupType::REGION;
        });
        $departments = array_filter($dtos, function ($dto) {
            return $dto->getType() === GroupType::DEPARTMENT;
        });
        usort($regions, function (TableDTO $a, TableDTO $b) {
            return strcmp($a->getName(),$b->getName());
        });
        usort($departments, function (TableDTO $a, TableDTO $b) {
            return strcmp($a->getName(),$b->getName());
        });
        $return = [];
        foreach ($regions as $region) {
            $return[] = $region;
            foreach ($departments as $department) {
                if ($department->getParentId() === $region->getId()) {
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

    public function getTableData(Group $group, CensusRequestData $censusRequestData) {
        $groupIds = array_filter($this->statisticGroupRepository->findAllRelevantChildGroups($group->getId()), function ($id) use ($group) { // We need to filter because the function also returns the group itself
            return !($id === $group->getId());
        });
        $flattenedGroups = $this->flattenGroupTree($groupIds);

        $dataTransferObjects = [];
        $relevantYears = range(date('Y') - 5, date('Y'));
        foreach ($flattenedGroups as $flattenedGroup) {
            $dataTransferObjects[] = CensusMapper::MapToCensusTable($flattenedGroup, $this->censusGroupRepository->findBy(['group_id' => $flattenedGroup->getId()]), $relevantYears);
        }
        return [
            'years' => $relevantYears,
            'data' => $this->sortDTOs($dataTransferObjects),
        ];
    }
}

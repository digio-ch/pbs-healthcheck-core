<?php

namespace App\Service\DataProvider;

use App\Entity\Midata\Group;
use App\Repository\Midata\CensusGroupRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class CensusDataProvider extends WidgetDataProvider
{

    private CensusGroupRepository $censusGroupRepository;
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        CensusGroupRepository $censusGroupRepository
    ) {
        $this->censusGroupRepository = $censusGroupRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    public function getPreviewData(Group $group) {
        $groups = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($group->getId(), ['Group::Abteilung', 'Group::Kantonalverband', 'Group::Region']);
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

    public function getTableData(Group $group) {
        $groups = $this->groupRepository->findAllRelevantSubGroupsByParentGroupId($group->getId(), ['Group::Abteilung', 'Group::Kantonalverband', 'Group::Region']);
        return $groups;
    }
}

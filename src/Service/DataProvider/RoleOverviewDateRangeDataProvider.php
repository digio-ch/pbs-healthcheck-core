<?php

namespace App\Service\DataProvider;

use App\DTO\Mapper\RoleOverviewMapper;
use App\DTO\Model\Apps\Widgets\RoleOverview\RoleOccupationWrapper;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedPersonRoleRepository;
use App\Repository\Midata\GroupRepository;
use App\Repository\Midata\GroupTypeRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoleOverviewDateRangeDataProvider extends WidgetDataProvider
{
    protected $personRoleRepository;
    public function __construct(
        GroupRepository $groupRepository,
        GroupTypeRepository $groupTypeRepository,
        TranslatorInterface $translator,
        AggregatedPersonRoleRepository $personRoleRepository
    ) {
        $this->groupRepository = $groupRepository;
        $this->personRoleRepository = $personRoleRepository;
        parent::__construct(
            $groupRepository,
            $groupTypeRepository,
            $translator
        );
    }

    public function getData(Group $group, string $from, string $to)
    {
        $dto = RoleOverviewMapper::createRoleOverviewDTO($group);

        $aggregatedPersonRoles = $this->personRoleRepository->findByGroupInTimeframe($group, $from, $to);
        /** @var RoleOccupationWrapper[] $roleOccupationWrappers */
        $roleOccupationWrappers = [];
        foreach ($aggregatedPersonRoles as $aggregatedPersonRole) {
            $wrapperExistsAt = -1;
            foreach ($roleOccupationWrappers as $key => $roleOccupationWrapper) {
                if ($roleOccupationWrapper->getRoleType() === $aggregatedPersonRole->getRole()->getRoleType()) {
                    $wrapperExistsAt = $key;
                }
            }
            if ($wrapperExistsAt !== -1) {
                $roleOccupationWrappers[$wrapperExistsAt]->addData(RoleOverviewMapper::createRoleOccupation($aggregatedPersonRole, $from, $to));
            } else {
                $wrapper = RoleOverviewMapper::createRoleOccupationWrapper($aggregatedPersonRole->getRole(), $this->translator->getLocale());
                $wrapper->addData(RoleOverviewMapper::createRoleOccupation($aggregatedPersonRole, $from, $to));
                $roleOccupationWrappers[] = $wrapper;
            }
        }
        $dto->setData($roleOccupationWrappers);
        return $dto;
    }
}

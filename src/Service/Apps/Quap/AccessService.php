<?php

namespace App\Service\Apps\Quap;

use App\Entity\Midata\Group;
use App\Entity\Midata\GroupType;
use App\Service\Apps\Quap\Exception\InvalidGroupTypeException;
use App\Service\Apps\Quap\Exception\InvalidParentGroupTypeException;

readonly class AccessService
{
    /**
     * Returns the group types the given group is allowed to access
     *
     * It does not make sense for the federation to be able to inspect a department. Therefore, we have to validate the access.
     *
     *  There are the following access rules:
     *  - federation => canton/region
     *  - canton => region/department
     *  - region => department
     *
     * @param Group $group
     * @return string[]
     * @throws InvalidParentGroupTypeException
     */
    protected function getSubordinateGroupTypes(Group $group): array
    {
        $groupType = $group->getGroupType()->getGroupType();

        return match ($groupType) {
            GroupType::FEDERATION => [GroupType::CANTON, GroupType::REGION],
            GroupType::CANTON => [GroupType::REGION, GroupType::DEPARTMENT],
            GroupType::REGION => [GroupType::DEPARTMENT],
            default => throw new InvalidParentGroupTypeException(
                sprintf(
                    "group type %s (%s) has no access to shared questionnaires",
                    $groupType,
                    $group->getId(),
                ),
            )
        };
    }

    /**
     * @throws InvalidGroupTypeException
     */
    protected function validateQuapAccess(Group $group): void
    {
        $groupType = $group->getGroupType()->getGroupType();

        $isValid = in_array($groupType, [GroupType::DEPARTMENT, GroupType::REGION, GroupType::CANTON], true);

        if ($isValid) {
            return;
        };

        throw new InvalidGroupTypeException();
    }
}

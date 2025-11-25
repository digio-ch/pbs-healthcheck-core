<?php

namespace App\Model;

use App\Entity\Midata\Group;

class OverviewPreview
{
    /**
     * @var Group $group
     */
    private Group $group;
    /**
     * @var array<string, int> $groupTypes
     */
    private array $groupTypes;

    /**
     * @param Group $group
     * @param array<string, int> $groupTypes
     */
    public function __construct(Group $group, array $groupTypes = [])
    {
        $this->group = $group;
        $this->groupTypes = $groupTypes;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getGroupTypes(): array
    {
        return $this->groupTypes;
    }

    public function setGroupTypes(array $groupTypes): void
    {
        $this->groupTypes = $groupTypes;
    }
}

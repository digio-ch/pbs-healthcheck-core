<?php

namespace App\DTO\Model\FilterRequestData;

use App\Entity\Midata\Group;

class WidgetOfDepartmentRequestData extends WidgetRequestData
{
    /**
     * @var Group
     */
    private Group $department;

    public function getDepartment(): Group
    {
        return $this->department;
    }

    public function setDepartment(Group $department): void
    {
        $this->department = $department;
    }
}

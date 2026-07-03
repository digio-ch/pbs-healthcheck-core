<?php

namespace App\DTO\Model\Apps\Census;

class MembersWidgetDTO
{
    /**
     * @var StackedBarElementDTO[]
     */
    private array $data;

    /**
     * @return StackedBarElementDTO[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}

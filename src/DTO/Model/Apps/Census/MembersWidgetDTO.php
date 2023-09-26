<?php

namespace App\DTO\Model\Apps\Census;

class MembersWidgetDTO
{
    /**
     * @var StackedBarElementDTO[]
     */
    private array $data;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

}

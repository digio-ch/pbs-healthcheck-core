<?php

declare(strict_types=1);

namespace App\DTO\Model\Charts;

class LineChartDataPointDTO
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $value;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}

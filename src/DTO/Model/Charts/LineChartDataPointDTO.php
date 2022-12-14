<?php

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

    /**
     * @param string $name
     */
    public function setName(string $name)
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

    /**
     * @param int $value
     */
    public function setValue(int $value)
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

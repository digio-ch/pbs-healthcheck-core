<?php

namespace App\DTO\Model\Apps\Census;

class StackedBarElementDTO
{
    private int $y;
    private string $x;
    private string $color;

    /**
     * @param int $y
     * @param string $x
     * @param string $color
     */
    public function __construct(int $y, string $x, string $color)
    {
        $this->y = $y;
        $this->x = $x;
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }

    /**
     * @return string
     */
    public function getX(): string
    {
        return $this->x;
    }

    /**
     * @param string $x
     */
    public function setX(string $x): void
    {
        $this->x = $x;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}

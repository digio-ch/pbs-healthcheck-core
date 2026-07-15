<?php

namespace App\DTO\Model\Apps;

use Generator;

class CsvFile
{
    /**
     * @var string UTF8 filename
     */
    private string $name;

    /**
     * @var string ASCII fallback filename
     */
    private string $fallbackName;

    private Generator $rows;

    /**
     * @param string $name
     * @param string $fallbackName
     * @param Generator $rows
     */
    public function __construct(string $name, string $fallbackName, Generator $rows)
    {
        $this->name = $name;
        $this->fallbackName = $fallbackName;
        $this->rows = $rows;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFallbackName(): string
    {
        return $this->fallbackName;
    }

    public function setFallbackName(string $fallbackName): void
    {
        $this->fallbackName = $fallbackName;
    }

    public function getRows(): Generator
    {
        return $this->rows;
    }

    public function setRows(Generator $rows): void
    {
        $this->rows = $rows;
    }
}

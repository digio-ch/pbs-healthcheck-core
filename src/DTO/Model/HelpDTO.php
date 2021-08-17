<?php

namespace App\DTO\Model;

class HelpDTO {

    /**
     * @var string $help
     */
    private $help;

    /**
     * @var int $severity
     */
    private $severity;

    /**
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * @param string $help
     */
    public function setHelp(string $help): void
    {
        $this->help = $help;
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

    /**
     * @param int $severity
     */
    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }
}
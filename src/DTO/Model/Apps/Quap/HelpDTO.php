<?php

namespace App\DTO\Model\Apps\Quap;

class HelpDTO
{

    /**
     * @var string $help
     */
    private $help;

    /**
     * @var int $severity
     */
    private $severity;

    /**
     * @var LinkDTO[] $links
     */
    private $links;

    public function __construct()
    {
        $this->links = [];
    }

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

    /**
     * @return LinkDTO[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function addLink(LinkDTO $linkDTO): void
    {
        array_push($this->links, $linkDTO);
    }
}

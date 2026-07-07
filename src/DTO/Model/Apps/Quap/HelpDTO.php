<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Quap;

class HelpDTO
{
    private string $help;

    private int $severity = 0;

    /**
     * @var LinkDTO[] $links
     */
    private array $links = [];

    public function getHelp(): string
    {
        return $this->help;
    }

    public function setHelp(string $help): void
    {
        $this->help = $help;
    }

    public function getSeverity(): int
    {
        return $this->severity;
    }

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
        $this->links[] = $linkDTO;
    }
}

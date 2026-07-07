<?php

declare(strict_types=1);

namespace App\DTO\Model\Gamification;

class PersonGamificationDTO
{
    private string $name;
    private string $title;
    private string $levelKey;
    /** @var LevelDTO[] $levels */
    private array $levels;
    private bool $betaRequested;

    public function isBetaRequested(): bool
    {
        return $this->betaRequested;
    }

    public function setBetaRequested(bool $betaRequested): void
    {
        $this->betaRequested = $betaRequested;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLevelKey(): string
    {
        return $this->levelKey;
    }

    public function setLevelKey(string $levelKey): void
    {
        $this->levelKey = $levelKey;
    }

    /**
     * @return LevelDTO[]
     */
    public function getLevels(): array
    {
        return $this->levels;
    }

    public function setLevels(array $levels): void
    {
        $this->levels = $levels;
    }
}

<?php

namespace App\DTO\Model\Gamification;

class PersonGamificationDTO
{
    private string $name;
    private string $title;
    private string $levelKey;
    private bool $levelUp;
    /** @var LevelDTO[] $levels */
    private array $levels;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLevelKey(): string
    {
        return $this->levelKey;
    }

    /**
     * @param string $levelKey
     */
    public function setLevelKey(string $levelKey): void
    {
        $this->levelKey = $levelKey;
    }

    /**
     * @return bool
     */
    public function isLevelUp(): bool
    {
        return $this->levelUp;
    }

    /**
     * @param bool $levelUp
     */
    public function setLevelUp(bool $levelUp): void
    {
        $this->levelUp = $levelUp;
    }

    /**
     * @return array
     */
    public function getLevels(): array
    {
        return $this->levels;
    }

    /**
     * @param array $levels
     */
    public function setLevels(array $levels): void
    {
        $this->levels = $levels;
    }
}

<?php

namespace App\DTO\Model\Gamification;

class LevelDTO
{
    private string $title;
    private int $key;
    /** @var GoalDTO[] $goals */
    private array $goals;
    private bool $active;
    private int $required;

    /**
     * @return int
     */
    public function getRequired(): int
    {
        return $this->required;
    }

    /**
     * @param int $required
     */
    public function setRequired(int $required): void
    {
        $this->required = $required;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

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
     * @return int
     */
    public function getKey(): int
    {
        return $this->key;
    }

    /**
     * @param int $key
     */
    public function setKey(int $key): void
    {
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function getGoals(): array
    {
        return $this->goals;
    }

    /**
     * @param array $goals
     */
    public function setGoals(array $goals): void
    {
        $this->goals = $goals;
    }

}

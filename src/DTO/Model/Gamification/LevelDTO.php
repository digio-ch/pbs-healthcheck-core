<?php

declare(strict_types=1);

namespace App\DTO\Model\Gamification;

class LevelDTO
{
    private string $title;
    private int $key;
    /** @var GoalDTO[] $goals */
    private array $goals;
    private bool $active;
    private int $required;
    private ?string $access;

    public function getRequired(): int
    {
        return $this->required;
    }

    public function setRequired(int $required): void
    {
        $this->required = $required;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key): void
    {
        $this->key = $key;
    }

    /**
     * @return GoalDTO[]
     */
    public function getGoals(): array
    {
        return $this->goals;
    }

    public function setGoals(array $goals): void
    {
        $this->goals = $goals;
    }

    public function getAccess(): ?string
    {
        return $this->access;
    }

    public function setAccess(?string $access): void
    {
        $this->access = $access;
    }
}

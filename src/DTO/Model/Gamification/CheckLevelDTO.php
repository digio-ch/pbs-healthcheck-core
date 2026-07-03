<?php

declare(strict_types=1);

namespace App\DTO\Model\Gamification;

class CheckLevelDTO
{
    private bool $levelUp;
    private string $title;

    public function isLevelUp(): bool
    {
        return $this->levelUp;
    }

    public function setLevelUp(bool $levelUp): void
    {
        $this->levelUp = $levelUp;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}

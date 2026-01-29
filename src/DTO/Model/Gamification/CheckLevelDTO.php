<?php

namespace App\DTO\Model\Gamification;

class CheckLevelDTO
{
    private bool $levelUp;
    private string $title;

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
}
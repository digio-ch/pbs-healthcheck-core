<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Widgets;

class QualificationDTO
{
    private ?string $state = null;

    private ?string $shortName = null;

    private ?string $fullName = null;

    private string $eventOrigin;

    private string $expiresAt;

    private string $color;

    /**
     * @param string $state
     */
    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $shortName
     */
    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }
    public function getEventOrigin(): string
    {
        return $this->eventOrigin;
    }

    public function setEventOrigin(string $eventOrigin): void
    {
        $this->eventOrigin = $eventOrigin;
    }

    public function getExpiresAt(): string
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}

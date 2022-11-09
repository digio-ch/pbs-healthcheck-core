<?php

namespace App\DTO\Model\Apps\Widgets;

class QualificationDTO
{
    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $eventOrigin;

    /**
     * @var string
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $color;

    /**
     * @param string $state
     */
    public function setState(?string $state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $shortName
     */
    public function setShortName(?string $shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }
    /**
     * @return string
     */
    public function getEventOrigin(): string
    {
        return $this->eventOrigin;
    }

    /**
     * @param string $eventOrigin
     */
    public function setEventOrigin(string $eventOrigin): void
    {
        $this->eventOrigin = $eventOrigin;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return $this->expiresAt;
    }

    /**
     * @param string $expiresAt
     */
    public function setExpiresAt(string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}

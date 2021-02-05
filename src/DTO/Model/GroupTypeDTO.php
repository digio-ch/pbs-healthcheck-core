<?php

namespace App\DTO\Model;

class GroupTypeDTO
{
    /** @var int|null */
    private $id;
    /** @var string|null */
    private $groupType;
    /** @var string|null */
    private $label;
    /** @var string */
    private $color;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    /**
     * @param string|null $groupType
     */
    public function setGroupType(?string $groupType): void
    {
        $this->groupType = $groupType;
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

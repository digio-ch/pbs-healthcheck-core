<?php

namespace App\DTO\Model;

class GroupTypeDTO
{
    private ?int $id = null;
    private ?string $groupType = null;
    private ?string $label = null;
    private string $color;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    public function setGroupType(?string $groupType): void
    {
        $this->groupType = $groupType;
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

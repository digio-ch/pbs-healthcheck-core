<?php

namespace App\DTO\Model\Apps\Quap;

class NestedExtendedAnswersDTO
{
    private ?ExtendedAnswersDTO $value;

    /**
     * @var NestedExtendedAnswersDTO[] $children
     */
    private array $children;

    /**
     * @param NestedExtendedAnswersDTO[] $children
     */
    public function __construct(?ExtendedAnswersDTO $value = null, array $children = [])
    {
        $this->value = $value;
        $this->children = $children;
    }


    public function addChild(NestedExtendedAnswersDTO $child): void
    {
        $this->children[] = $child;
    }

    public function getValue(): ?ExtendedAnswersDTO
    {
        return $this->value;
    }

    public function setValue(?ExtendedAnswersDTO $value): void
    {
        $this->value = $value;
    }

    /**
     * @return NestedExtendedAnswersDTO[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }
}

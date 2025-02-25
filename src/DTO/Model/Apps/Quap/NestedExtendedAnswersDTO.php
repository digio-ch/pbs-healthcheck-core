<?php

namespace App\DTO\Model\Apps\Quap;

class NestedExtendedAnswersDTO
{
    /**
     * @var ?ExtendedAnswersDTO $value
     */
    private ?ExtendedAnswersDTO $value;

    /**
     * @var NestedExtendedAnswersDTO[] $children
     */
    private array $children;

    /**
     * @param ExtendedAnswersDTO|null $value
     * @param NestedExtendedAnswersDTO[] $children
     */
    public function __construct(?ExtendedAnswersDTO $value = null, array $children = [])
    {
        $this->value = $value;
        $this->children = $children;
    }


    /**
     * @param NestedExtendedAnswersDTO $child
     * @return void
     */
    public function addChild(NestedExtendedAnswersDTO $child): void
    {
        $this->children[] = $child;
    }

    /**
     * @return ExtendedAnswersDTO|null
     */
    public function getValue(): ?ExtendedAnswersDTO
    {
        return $this->value;
    }

    /**
     * @param ExtendedAnswersDTO|null $value
     * @return void
     */
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

    /**
     * @param array $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }
}

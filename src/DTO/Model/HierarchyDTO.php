<?php

namespace App\DTO\Model;


/**
 * @template T
 */
class HierarchyDTO
{

    /**
     * @var ?T $parent
     */
    private $parent;

    /**
     * @var array<HierarchyDTO<T>> $children
     */
    private array $children;


    /**
     * @param ?T $node
     * @param HierarchyDTO<T>[] $children
     */
    public function __construct($node, array $children = array()) {
        $this->parent = $node;
        $this->children = $children;
    }

    /**
     * @return ?T
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param T $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return HierarchyDTO<T>[]
     */
    public function &getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param HierarchyDTO<T>[] $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @param HierarchyDTO<T> $child
     * @return void
     */
    public function addChild(HierarchyDTO $child): void
    {
        $this->children[] = $child;
    }
}
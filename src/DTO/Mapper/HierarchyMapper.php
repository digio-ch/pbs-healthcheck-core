<?php

namespace App\DTO\Mapper;
use App\DTO\Model\HierarchyDTO;

/**
 * HierarchyMapper maps the HierarchyDTO of type A to a HierarchyDTO of type B
 * @template A
 * @Template B
 */
class HierarchyMapper
{

    /**
     * @param HierarchyDTO<A> $nodeDTO
     * @param callable $mapper
     * @return HierarchyDTO<B>
     */
    public static function mapNode(HierarchyDTO $nodeDTO, callable $mapper): HierarchyDTO
    {
        $node = $nodeDTO->getParent();
        if ($node !== null) {
            $nodeDTO->setParent($mapper($node));
        }

        if (count($nodeDTO->getChildren()) > 0) {
            $nodeDTO->setChildren(array_map(
                fn($child) => HierarchyMapper::mapNode($child, $mapper),
                $nodeDTO->getChildren()
            ));
        }

        return $nodeDTO;
    }
}
<?php

namespace App\Model;

use App\Entity\Aggregated\AggregatedQuap;
use Tree\Node\Node;

class QuapNode extends Node
{
    /**
     * @param AggregatedQuap $value
     * @param Node[] $children
     */
    public function __construct($value = null, array $children = [])
    {
        parent::__construct($value, $children);
    }

    /**
     * @return AggregatedQuap
     */
    public function getQuap(): AggregatedQuap
    {
        return $this->getValue();
    }

    public function hasSameGroupType(QuapNode $other): bool
    {
        try {
            $groupTypeID = $this->getQuap()->getGroup()->getGroupType()->getId();
            $otherGroupTypeID = $other->getQuap()->getGroup()->getGroupType()->getId();

            return $groupTypeID === $otherGroupTypeID;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isGroupParent(QuapNode $child): bool
    {
        try {
            $parentGroupID = $child->getQuap()->getGroup()->getParentGroup()->getId();

            return $parentGroupID === $this->getQuap()->getGroup()->getId();
        } catch (\Throwable $e) {
            return false;
        }
    }
}

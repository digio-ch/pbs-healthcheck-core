<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Quap\NestedExtendedAnswersDTO;
use App\Model\QuapNode;

class QuapNodeMapper
{
    public static function map(QuapNode $tree): NestedExtendedAnswersDTO
    {
        $dto = AnswersMapper::mapNestedExtendedAnswers($tree->getQuap());

        foreach ($tree->getChildren() as $child) {
            $childDto = self::map($child);
            $dto->addChild($childDto);
        }

        return $dto;
    }
}

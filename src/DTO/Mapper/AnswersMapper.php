<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\Entity\Aggregated\AggregatedQuap;

class AnswersMapper
{
    public static function mapAnswers(AggregatedQuap $widgetQuap): AnswersDTO
    {
        $dto = new AnswersDTO();
        $dto->setAnswers($widgetQuap->getAnswers());
        $dto->setComputedAnswers($widgetQuap->getComputedAnswers());
        $dto->setShareAccess($widgetQuap->getAllowAccess());
        return $dto;
    }
}

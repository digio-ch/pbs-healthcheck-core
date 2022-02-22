<?php

namespace App\DTO\Mapper;

use App\DTO\Model\AnswersDTO;
use App\Entity\WidgetQuap;

class AnswersMapper
{
    public static function mapAnswers(WidgetQuap $widgetQuap): AnswersDTO
    {
        $dto = new AnswersDTO();
        $dto->setAnswers($widgetQuap->getAnswers());
        $dto->setComputedAnswers($widgetQuap->getComputedAnswers());
        $dto->setShareAccess($widgetQuap->getAllowAccess());
        return $dto;
    }
}

<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\DTO\Model\Apps\Quap\ExtendedAnswersDTO;
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

    public static function mapExtendedAnswers(AggregatedQuap $widgetQuap): ExtendedAnswersDTO
    {
        $answerGroup = $widgetQuap->getGroup();

        $dto = new ExtendedAnswersDTO();
        $dto->setAnswers($widgetQuap->getAnswers());
        $dto->setComputedAnswers($widgetQuap->getComputedAnswers());
        $dto->setGroupId($answerGroup->getId());
        $dto->setGroupName($answerGroup->getName());
        $dto->setGroupTypeId($answerGroup->getGroupType()->getId());
        $dto->setGroupType($answerGroup->getGroupType()->getGroupType());

        return $dto;
    }
}

<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Quap\AnswersDTO;
use App\DTO\Model\Apps\Quap\ExtendedAnswersDTO;
use App\DTO\Model\Apps\Quap\NestedExtendedAnswersDTO;
use App\Entity\Aggregated\AggregatedQuap;

class AnswersMapper
{
    public static function mapAnswers(AggregatedQuap $widgetQuap): AnswersDTO
    {
        // we want to reverse sort the aspects so that the JSON parser encodes them as object instead of array
        $aspects = AnswersMapper::reverseSortAspects($widgetQuap->getAnswers());

        $dto = new AnswersDTO();
        $dto->setAnswers($aspects);
        $dto->setComputedAnswers($widgetQuap->getComputedAnswers());
        $dto->setShareAccess($widgetQuap->getAllowAccess());
        return $dto;
    }

    public static function mapExtendedAnswers(AggregatedQuap $widgetQuap): ExtendedAnswersDTO
    {
        $answerGroup = $widgetQuap->getGroup();

        // we want to reverse sort the aspects so that the JSON parser encodes them as object instead of array
        $aspects = AnswersMapper::reverseSortAspects($widgetQuap->getAnswers());

        $dto = new ExtendedAnswersDTO();
        $dto->setAnswers($aspects);
        $dto->setComputedAnswers($widgetQuap->getComputedAnswers());
        $dto->setGroupId($answerGroup->getId());
        $dto->setGroupName($answerGroup->getName());
        $dto->setGroupTypeId($answerGroup->getGroupType()->getId());
        $dto->setGroupType($answerGroup->getGroupType()->getGroupType());

        return $dto;
    }

    /**
     * @param AggregatedQuap $quap
     * @return NestedExtendedAnswersDTO
     */
    public static function mapNestedExtendedAnswers(AggregatedQuap $quap): NestedExtendedAnswersDTO
    {
        $extendedAnswer = self::mapExtendedAnswers($quap);

        return new NestedExtendedAnswersDTO($extendedAnswer, []);
    }

    /**
     * @param array<string,int[]> $aspects
     * @return array<string,int[]>
     */
    public static function reverseSortAspects(array $aspects): array
    {
        foreach ($aspects as $questionId => $_) {
            krsort($aspects[$questionId]);
        }

        krsort($aspects);

        return $aspects;
    }
}

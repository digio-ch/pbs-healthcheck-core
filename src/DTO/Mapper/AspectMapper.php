<?php

namespace App\DTO\Mapper;

use App\DTO\Model\AspectDTO;
use App\DTO\Model\QuestionDTO;
use App\Entity\Aspect;
use DateTime;

class AspectMapper {

    public static function createAspectFromEntity(Aspect $aspect, string $locale, DateTime $dateTime): ?AspectDTO {

        if (
            ($aspect->getDeletedAt() && // is needed, otherwise it throws an error if null
            date_diff($dateTime, $aspect->getDeletedAt())->invert == 1)
            || date_diff($dateTime, $aspect->getCreatedAt())->invert == 0
        ) {
            return null;
        }

        $dto = new AspectDTO();
        $questionDTO = new QuestionDTO();

        $dto->setId($aspect->getLocalId());

        switch ($locale) {
            case (str_contains($locale, "it")):
                $dto->setName($aspect->getNameIt());
                break;
            case (str_contains($locale, "fr")):
                $dto->setName($aspect->getNameFr());
                break;
            default:
                $dto->setName($aspect->getNameDe());
                break;
        }

        foreach ($aspect->getQuestions() as $question) {
            $questionDTO = QuestionMapper::createQuestionFromEntity($question, $locale, $dateTime);

            if ($questionDTO) {
                $dto->addQuestion($questionDTO);
            }
        }

        return $dto;
    }

}
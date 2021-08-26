<?php

namespace App\DTO\Mapper;

use App\DTO\Model\AspectDTO;
use App\Entity\Aspect;

class AspectMapper
{

    public static function createAspectFromEntity(Aspect $aspect, string $locale): AspectDTO
    {
        $dto = new AspectDTO();

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

        if ($aspect->getQuestions()) {
            foreach ($aspect->getQuestions() as $question) {
                $dto->addQuestion(QuestionMapper::createQuestionFromEntity($question, $locale));
            }
        }
        return $dto;
    }

}
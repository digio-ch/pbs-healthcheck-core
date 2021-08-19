<?php

namespace App\DTO\Mapper;

use App\DTO\Model\QuestionnaireDTO;
use App\Entity\Questionnaire;
use DateTime;

class QuestionnaireMapper
{

    public static function createQuestionnaireFromEntity(Questionnaire $questionnaire, string $locale, DateTime $dateTime): QuestionnaireDTO
    {

        $questionnaireDTO = new QuestionnaireDTO();
        $questionnaireDTO->setId($questionnaire->getId());
        $questionnaireDTO->setType($questionnaire->getType());

        if ($questionnaire->getAspects()) {
            foreach ($questionnaire->getAspects() as $aspect) {
                $questionnaireDTO->addAspect(AspectMapper::createAspectFromEntity($aspect, $locale, $dateTime));
            }
        }

        return $questionnaireDTO;
    }
}
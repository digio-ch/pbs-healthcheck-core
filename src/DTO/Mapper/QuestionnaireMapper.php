<?php

namespace App\DTO\Mapper;

use App\DTO\Model\Apps\Quap\QuestionnaireDTO;
use App\Entity\Quap\Questionnaire;

class QuestionnaireMapper
{
    public static function createQuestionnaireFromEntity(Questionnaire $questionnaire, string $locale): QuestionnaireDTO
    {

        $questionnaireDTO = new QuestionnaireDTO();
        $questionnaireDTO->setId($questionnaire->getId());
        $questionnaireDTO->setType($questionnaire->getType());

        if ($questionnaire->getAspects()) {
            foreach ($questionnaire->getAspects() as $aspect) {
                $questionnaireDTO->addAspect(AspectMapper::createAspectFromEntity($aspect, $locale));
            }
        }

        return $questionnaireDTO;
    }
}

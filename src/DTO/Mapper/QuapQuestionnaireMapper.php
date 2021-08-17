<?php

namespace App\DTO\Mapper;

use App\DTO\Model\AspectDTO;
use App\Entity\Questionnaire;
use DateTime;

class QuapQuestionnaireMapper { // this name might not be all too fitting

    public static function createFromEntities(array $questionnaires, string $locale, DateTime $dateTime): array {
        $aspectDTOs = [];
        $aspectDTO = new AspectDTO();

        foreach ($questionnaires as $questionnaire) {
            foreach ($questionnaire->getAspects() as $aspect) {
                $aspectDTO = AspectMapper::createAspectFromEntity($aspect, $locale, $dateTime);

                if ($aspectDTO) {
                    array_push($aspectDTOs, $aspectDTO);
                }
            }
        }


        return $aspectDTOs;
    }
}
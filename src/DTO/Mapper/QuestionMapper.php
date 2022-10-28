<?php

namespace App\DTO\Mapper;

use App\DTO\Model\QuestionDTO;
use App\Entity\quap\Question;

class QuestionMapper
{

    public static function createQuestionFromEntity(Question $question, string $locale): QuestionDTO
    {

        $dto = new QuestionDTO();

        $dto->setId($question->getLocalId());
        $dto->setAnswerOptions($question->getAnswerOptions());

        switch ($locale) {
            case (str_contains($locale, "it")):
                $dto->setQuestion($question->getQuestionIt());
                break;
            case (str_contains($locale, "fr")):
                $dto->setQuestion($question->getQuestionFr());
                break;
            default:
                $dto->setQuestion($question->getQuestionDe());
                break;
        }

        if ($question->getHelp()) {
            foreach ($question->getHelp() as $help) {
                $dto->addHelp(HelpMapper::createHelpFromEntity($help, $locale));
            }
        }

        return $dto;
    }
}

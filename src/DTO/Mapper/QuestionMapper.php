<?php

namespace App\DTO\Mapper;

use App\DTO\Model\HelpDTO;
use App\DTO\Model\QuestionDTO;
use App\Entity\Question;

class QuestionMapper {

    
    public static function createQuestionFromEntity(Question $question, string $locale, \DateTime $dateTime): ?QuestionDTO {

        if (
            ($question->getDeletedAt() &&
            date_diff($dateTime, $question->getDeletedAt())->invert == 1)
            || date_diff($dateTime, $question->getCreatedAt())->invert == 0
        ) {
            return null;
        }

        $dto = new QuestionDTO();
        $helpDTO = new HelpDTO();

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

        foreach ($question->getHelp() as $help) {
            $helpDTO = HelpMapper::createHelpFromEntity($help, $locale, $dateTime);

            if ($helpDTO) {
                $dto->addHelp($helpDTO);
            }
        }

        return $dto;
    }
}
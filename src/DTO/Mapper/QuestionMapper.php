<?php

namespace App\DTO\Mapper;

use Doctrine\Common\Collections\Collection;
use App\DTO\Model\Apps\Quap\QuestionDTO;
use App\Entity\Quap\Question;

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

        if ($question->getHelp() instanceof Collection) {
            foreach ($question->getHelp() as $help) {
                $dto->addHelp(HelpMapper::createHelpFromEntity($help, $locale));
            }
        }

        return $dto;
    }
}

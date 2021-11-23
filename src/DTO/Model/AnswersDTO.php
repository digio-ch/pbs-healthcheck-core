<?php

namespace App\DTO\Model;

class AnswersDTO
{
    /** @var array $answers */
    private $answers;

    /** @var array $computed_answers */
    private $computed_answers;

    /**
     * @return array
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @param array $answers
     */
    public function setAnswers(array $answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @return array
     */
    public function getComputedAnswers(): array
    {
        return $this->computed_answers;
    }

    /**
     * @param array $computed_answers
     */
    public function setComputedAnswers(array $computed_answers): void
    {
        $this->computed_answers = $computed_answers;
    }
}

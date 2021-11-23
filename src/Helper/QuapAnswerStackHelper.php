<?php

namespace App\Helper;

class QuapAnswerStackHelper
{
    /** @var array $answerStack */
    private $answerStack;

    public function __construct(array $answerStack) {
        $this->answerStack = $answerStack;
    }

    public function getAnswer(int $aspectId, int $questionId): ?int {
        return $this->answerStack[$aspectId][$questionId];
    }

    public function setAnswer(int $aspectId, int $questionId, int $answer): void {
        $this->answerStack[$aspectId][$questionId] = $answer;
    }
}

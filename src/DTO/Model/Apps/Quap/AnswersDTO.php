<?php

namespace App\DTO\Model\Apps\Quap;

class AnswersDTO
{
    /** @var mixed[] $answers */
    private array $answers;

    /** @var mixed[] $computed_answers */
    private array $computed_answers;

    private bool $share_access;

    /**
     * @return mixed[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): void
    {
        $this->answers = $answers;
    }

    /**
     * @return mixed[]
     */
    public function getComputedAnswers(): array
    {
        return $this->computed_answers;
    }

    public function setComputedAnswers(array $computed_answers): void
    {
        $this->computed_answers = $computed_answers;
    }

    public function isShareAccess(): bool
    {
        return $this->share_access;
    }

    public function setShareAccess(bool $share_access): void
    {
        $this->share_access = $share_access;
    }
}

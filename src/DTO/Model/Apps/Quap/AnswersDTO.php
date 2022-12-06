<?php

namespace App\DTO\Model\Apps\Quap;

class AnswersDTO
{
    /** @var array $answers */
    private array $answers;

    /** @var array $computed_answers */
    private array $computed_answers;

    /** @var bool $share_access */
    private bool $share_access;

    /** @var bool $show_not_relevant */
    private bool $show_not_relevant;

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

    /**
     * @return bool
     */
    public function isShareAccess(): bool
    {
        return $this->share_access;
    }

    /**
     * @param bool $share_access
     */
    public function setShareAccess(bool $share_access): void
    {
        $this->share_access = $share_access;
    }

    /**
     * @return bool
     */
    public function isShowNotRelevant(): bool
    {
        return $this->show_not_relevant;
    }

    /**
     * @param bool $show_not_relevant
     */
    public function setShowNotRelevant(bool $show_not_relevant): void
    {
        $this->show_not_relevant = $show_not_relevant;
    }
}

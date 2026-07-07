<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Quap;

class QuestionDTO
{
    private int $id = 0;

    private string $question;

    private string $answerOptions;

    /**
     * @var HelpDTO[] $help
     */
    private array $help = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getAnswerOptions(): string
    {
        return $this->answerOptions;
    }

    public function setAnswerOptions(string $answerOptions): void
    {
        $this->answerOptions = $answerOptions;
    }

    /**
     * @return HelpDTO[]
     */
    public function getHelp(): array
    {
        return $this->help;
    }

    /**
     * @param HelpDTO[] $help
     */
    public function setHelp(array $help): void
    {
        $this->help = $help;
    }

    public function addHelp(HelpDTO $helpDTO): void
    {
        $this->help[] = $helpDTO;
    }
}

<?php

namespace App\DTO\Model\Apps\Quap;

class QuestionDTO
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $question
     */
    private $question;

    /**
     * @var string $answerOptions
     */
    private $answerOptions;

    /**
     * @var HelpDTO[] $help
     */
    private $help;

    public function __construct()
    {
        $this->help = [];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    /**
     * @return string
     */
    public function getAnswerOptions(): string
    {
        return $this->answerOptions;
    }

    /**
     * @param string $answerOptions
     */
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
        array_push($this->help, $helpDTO);
    }
}

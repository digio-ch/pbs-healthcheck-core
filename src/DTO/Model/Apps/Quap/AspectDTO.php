<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Quap;

class AspectDTO
{
    private int $id = 0;

    private string $name;

    private string $description;

    /**
     * @var QuestionDTO[] $questions
     */
    private array $questions = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return QuestionDTO[]|null
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * @param QuestionDTO[] $questions
     */
    public function setQuestions(array $questions): void
    {
        $this->questions = $questions;
    }

    public function addQuestion(QuestionDTO $questionDTO): void
    {
        $this->questions[] = $questionDTO;
    }
}

<?php

namespace App\DTO\Model;

class AspectDTO
{

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var QuestionDTO[] $questions
     */
    private $questions;

    public function __construct()
    {
        $this->questions = [];
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
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

    public function addQuestion(QuestionDTO $questionDTO)
    {
        array_push($this->questions, $questionDTO);
    }
}

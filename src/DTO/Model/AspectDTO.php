<?php

namespace App\DTO\Model;

use App\Entity\Question;
use Doctrine\Common\Collections\ArrayCollection;

class AspectDTO {

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

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

    public function addQuestion(QuestionDTO $questionDTO) {
        array_push($this->questions, $questionDTO);
    }
}
<?php

namespace App\DTO\Model\Apps\Quap;

class ExtendedAnswersDTO
{
    /** @var int $group_id */
    private int $group_id;

    /** @var string $group_name */
    private string $group_name;

    /** @var int $group_type_id */
    private int $group_type_id;

    /** @var string $group_type */
    private string $group_type;

    /** @var array $answers */
    private array $answers;

    /** @var array $computed_answers */
    private array $computed_answers;

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->group_id;
    }

    /**
     * @param int $group_id
     */
    public function setGroupId(int $group_id): void
    {
        $this->group_id = $group_id;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->group_name;
    }

    /**
     * @param string $group_name
     */
    public function setGroupName(string $group_name): void
    {
        $this->group_name = $group_name;
    }

    /**
     * @return int
     */
    public function getGroupTypeId(): int
    {
        return $this->group_type_id;
    }

    /**
     * @param int $group_type_id
     */
    public function setGroupTypeId(int $group_type_id): void
    {
        $this->group_type_id = $group_type_id;
    }

    /**
     * @return string
     */
    public function getGroupType(): string
    {
        return $this->group_type;
    }

    /**
     * @param string $group_type
     */
    public function setGroupType(string $group_type): void
    {
        $this->group_type = $group_type;
    }

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

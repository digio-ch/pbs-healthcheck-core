<?php

declare(strict_types=1);

namespace App\DTO\Model\Apps\Quap;

class ExtendedAnswersDTO
{
    private int $group_id;

    private string $group_name;

    private int $group_type_id;

    private string $group_type;

    /** @var mixed[] $answers */
    private array $answers;

    /** @var mixed[] $computed_answers */
    private array $computed_answers;

    public function getGroupId(): int
    {
        return $this->group_id;
    }

    public function setGroupId(int $group_id): void
    {
        $this->group_id = $group_id;
    }

    public function getGroupName(): string
    {
        return $this->group_name;
    }

    public function setGroupName(string $group_name): void
    {
        $this->group_name = $group_name;
    }

    public function getGroupTypeId(): int
    {
        return $this->group_type_id;
    }

    public function setGroupTypeId(int $group_type_id): void
    {
        $this->group_type_id = $group_type_id;
    }

    public function getGroupType(): string
    {
        return $this->group_type;
    }

    public function setGroupType(string $group_type): void
    {
        $this->group_type = $group_type;
    }

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
}

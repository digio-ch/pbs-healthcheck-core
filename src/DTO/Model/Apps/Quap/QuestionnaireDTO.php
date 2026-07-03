<?php

namespace App\DTO\Model\Apps\Quap;

class QuestionnaireDTO
{
    private int $id = 0;

    private string $type;

    /**
     * @var AspectDTO[] $aspects
     */
    private array $aspects;

    public function __construct()
    {
        $this->aspects = [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return AspectDTO[]
     */
    public function getAspects(): array
    {
        return $this->aspects;
    }

    /**
     * @param AspectDTO[] $aspects
     */
    public function setAspects(array $aspects): void
    {
        $this->aspects = $aspects;
    }

    public function addAspect(AspectDTO $aspect): void
    {
        $this->aspects[] = $aspect;
    }
}

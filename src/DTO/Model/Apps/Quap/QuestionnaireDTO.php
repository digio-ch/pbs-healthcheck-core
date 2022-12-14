<?php

namespace App\DTO\Model\Apps\Quap;

class QuestionnaireDTO
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $type
     */
    private $type;

    /**
     * @var AspectDTO[] $aspects
     */
    private $aspects;

    public function __construct()
    {
        $this->aspects = [];
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
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
        array_push($this->aspects, $aspect);
    }
}

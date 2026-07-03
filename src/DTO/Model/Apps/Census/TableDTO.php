<?php

namespace App\DTO\Model\Apps\Census;

class TableDTO
{
    private int $id;
    private int $parentId;
    private string $type;
    private bool $missing;
    private string $name;
    /**
     * @var int[] Array(6) of the last 6 Years
     */
    private array $absoluteMemberCounts = [];
    /**
     * @var int[] Array(3) with percentile changes
     */
    private array $relativeMemberCounts = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isMissing(): bool
    {
        return $this->missing;
    }

    public function setMissing(bool $missing): void
    {
        $this->missing = $missing;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int[]
     */
    public function getAbsoluteMemberCounts(): array
    {
        return $this->absoluteMemberCounts;
    }

    public function setAbsoluteMemberCounts(array $absoluteMemberCounts): void
    {
        $this->absoluteMemberCounts = $absoluteMemberCounts;
    }

    /**
     * @return int[]
     */
    public function getRelativeMemberCounts(): array
    {
        return $this->relativeMemberCounts;
    }

    public function setRelativeMemberCounts(array $relativeMemberCounts): void
    {
        $this->relativeMemberCounts = $relativeMemberCounts;
    }
}

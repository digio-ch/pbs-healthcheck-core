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
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
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
     * @return bool
     */
    public function isMissing(): bool
    {
        return $this->missing;
    }

    /**
     * @param bool $missing
     */
    public function setMissing(bool $missing): void
    {
        $this->missing = $missing;
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
     * @return array
     */
    public function getAbsoluteMemberCounts(): array
    {
        return $this->absoluteMemberCounts;
    }

    /**
     * @param array $absoluteMemberCounts
     */
    public function setAbsoluteMemberCounts(array $absoluteMemberCounts): void
    {
        $this->absoluteMemberCounts = $absoluteMemberCounts;
    }

    /**
     * @return array
     */
    public function getRelativeMemberCounts(): array
    {
        return $this->relativeMemberCounts;
    }

    /**
     * @param array $relativeMemberCounts
     */
    public function setRelativeMemberCounts(array $relativeMemberCounts): void
    {
        $this->relativeMemberCounts = $relativeMemberCounts;
    }
}

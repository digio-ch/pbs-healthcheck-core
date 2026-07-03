<?php

namespace App\DTO\Model\Apps\Widgets;

class LeaderOverviewDTO
{
    private ?string $name = null;

    private string $summaryMembersType;

    private string $summaryLeadersType;

    private ?int $fCount = null;

    private ?int $mCount = null;

    private int $uCount = 0;

    /**
     * @var LeaderDTO[]
     */
    private array $leaders = [];

    private string $color;

    public function setMCount(int $mCount): void
    {
        $this->mCount = $mCount;
    }

    public function getMCount(): ?int
    {
        return $this->mCount;
    }

    public function setFCount(int $fCount): void
    {
        $this->fCount = $fCount;
    }

    public function getFCount(): ?int
    {
        return $this->fCount;
    }

    public function getUCount(): int
    {
        return $this->uCount;
    }

    public function setUCount(int $uCount): void
    {
        $this->uCount = $uCount;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSummaryMembersType(): string
    {
        return $this->summaryMembersType;
    }

    public function setSummaryMembersType(string $summaryMembersType): void
    {
        $this->summaryMembersType = $summaryMembersType;
    }

    public function getSummaryLeadersType(): string
    {
        return $this->summaryLeadersType;
    }

    public function setSummaryLeadersType(string $summaryLeadersType): void
    {
        $this->summaryLeadersType = $summaryLeadersType;
    }

    /**
     * @return LeaderDTO[]
     */
    public function getLeaders(): array
    {
        return $this->leaders;
    }

    public function addLeader(LeaderDTO $leader): void
    {
        $this->leaders[] = $leader;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}

<?php

namespace App\DTO\Model\Apps\Widgets;

class LeaderOverviewDTO
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $summaryMembersType;

    /**
     * @var string
     */
    private $summaryLeadersType;

    /**
     * @var int
     */
    private $fCount;

    /**
     * @var int
     */
    private $mCount;

    /**
     * @var int
     */
    private $uCount;

    /**
     * @var LeaderDTO[]
     */
    private $leaders = [];

    /**
     * @var string
     */
    private $color;

    /**
     * @param int $mCount
     */
    public function setMCount(int $mCount)
    {
        $this->mCount = $mCount;
    }

    /**
     * @return mixed
     */
    public function getMCount()
    {
        return $this->mCount;
    }

    /**
     * @param int $fCount
     */
    public function setFCount(int $fCount)
    {
        $this->fCount = $fCount;
    }

    /**
     * @return mixed
     */
    public function getFCount()
    {
        return $this->fCount;
    }

    /**
     * @return int
     */
    public function getUCount(): int
    {
        return $this->uCount;
    }

    /**
     * @param int $uCount
     */
    public function setUCount(int $uCount): void
    {
        $this->uCount = $uCount;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSummaryMembersType(): string
    {
        return $this->summaryMembersType;
    }

    /**
     * @param string $summaryMembersType
     */
    public function setSummaryMembersType(string $summaryMembersType): void
    {
        $this->summaryMembersType = $summaryMembersType;
    }

    /**
     * @return string
     */
    public function getSummaryLeadersType(): string
    {
        return $this->summaryLeadersType;
    }

    /**
     * @param string $summaryLeadersType
     */
    public function setSummaryLeadersType(string $summaryLeadersType): void
    {
        $this->summaryLeadersType = $summaryLeadersType;
    }

    /**
     * @return LeaderDTO[]
     */
    public function getLeaders()
    {
        return $this->leaders;
    }

    /**
     * @param LeaderDTO $leader
     */
    public function addLeader(LeaderDTO $leader)
    {
        $this->leaders[] = $leader;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}

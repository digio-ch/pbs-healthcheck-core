<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_demographic_group')]
#[ORM\Index(columns: ['m_count'])]
#[ORM\Index(columns: ['f_count'])]
#[ORM\Index(columns: ['u_count'])]
#[ORM\Index(columns: ['m_count_leader'])]
#[ORM\Index(columns: ['f_count_leader'])]
#[ORM\Index(columns: ['u_count_leader'])]
#[ORM\Index(columns: ['group_type'])]
#[ORM\Index(columns: ['data_point_date'])]
#[ORM\Entity(repositoryClass: AggregatedDemographicGroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedDemographicGroup extends AggregatedEntity
{
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $mCount = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $fCount = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $uCount = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $mCountLeader = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $fCountLeader = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $uCountLeader = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $groupType = null;

    /**
     * AggregatedDemographicGroup constructor.
     */
    public function __construct()
    {
        $this->mCount = 0;
        $this->mCountLeader = 0;
        $this->fCount = 0;
        $this->fCountLeader = 0;
        $this->uCount = 0;
        $this->uCountLeader = 0;
    }


    public function setMCount(int $mCount): void
    {
        $this->mCount = $mCount;
    }

    public function getMCount(): ?int
    {
        return $this->mCount;
    }

    public function getUCount(): ?int
    {
        return $this->uCount;
    }

    /**
     * @param mixed $uCount
     */
    public function setUCount(?int $uCount): void
    {
        $this->uCount = $uCount;
    }

    public function getUCountLeader(): ?int
    {
        return $this->uCountLeader;
    }

    /**
     * @param mixed $uCountLeader
     */
    public function setUCountLeader(?int $uCountLeader): void
    {
        $this->uCountLeader = $uCountLeader;
    }

    public function setFCount(int $fCount): void
    {
        $this->fCount = $fCount;
    }

    public function getFCount(): ?int
    {
        return $this->fCount;
    }

    public function setMCountLeader(int $mCountLeader): void
    {
        $this->mCountLeader = $mCountLeader;
    }

    public function getMCountLeader(): ?int
    {
        return $this->mCountLeader;
    }

    public function setFCountLeader(int $fCountLeader): void
    {
        $this->fCountLeader = $fCountLeader;
    }

    public function getFCountLeader(): ?int
    {
        return $this->fCountLeader;
    }

    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function getGroupType(): ?string
    {
        return $this->groupType;
    }
}

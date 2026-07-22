<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedDemographicDepartmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_demographic_department')]
#[ORM\Index(columns: ['birthyear'])]
#[ORM\Index(columns: ['m_count'])]
#[ORM\Index(columns: ['f_count'])]
#[ORM\Index(columns: ['u_count'])]
#[ORM\Index(columns: ['m_count_leader'])]
#[ORM\Index(columns: ['f_count_leader'])]
#[ORM\Index(columns: ['u_count_leader'])]
#[ORM\Index(columns: ['group_type'])]
#[ORM\Index(columns: ['data_point_date'])]
#[ORM\Entity(repositoryClass: AggregatedDemographicDepartmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedDemographicDepartment extends AggregatedEntity
{
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $birthyear = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0])]
    private ?int $mCount = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0])]
    private ?int $fCount = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0])]
    private ?int $uCount = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0])]
    private ?int $mCountLeader = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0])]
    private ?int $fCountLeader = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 0])]
    private ?int $uCountLeader = 0;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $groupType = null;

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

    public function setBirthyear(int $birthyear): void
    {
        $this->birthyear = $birthyear;
    }

    public function getBirthyear(): ?int
    {
        return $this->birthyear;
    }
}

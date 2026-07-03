<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\Group;
use App\Repository\Aggregated\AggregatedDemographicCampGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_demographic_camp_group')]
#[ORM\Index(columns: ['m_count'])]
#[ORM\Index(columns: ['f_count'])]
#[ORM\Index(columns: ['u_count'])]
#[ORM\Index(columns: ['f_count_leader'])]
#[ORM\Index(columns: ['m_count_leader'])]
#[ORM\Index(columns: ['u_count_leader'])]
#[ORM\Index(columns: ['group_type'])]
#[ORM\Entity(repositoryClass: AggregatedDemographicCampGroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedDemographicCampGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: AggregatedDemographicCamp::class, inversedBy: 'demographicCampGroups')]
    private ?AggregatedDemographicCamp $demographicCamp = null;

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

    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Group::class)]
    private ?Group $group = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function getUCount(): ?int
    {
        return $this->uCount;
    }

    public function setUCount(int $uCount): void
    {
        $this->uCount = $uCount;
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

    /**
     * @return int
     */
    public function getUCountLeader(): ?int
    {
        return $this->uCountLeader;
    }

    public function setUCountLeader(int $uCountLeader): void
    {
        $this->uCountLeader = $uCountLeader;
    }

    /**
     * @return string
     */
    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function setDemographicCamp(AggregatedDemographicCamp $demographicCamp): void
    {
        $this->demographicCamp = $demographicCamp;
    }

    public function getDemographicCamp(): ?AggregatedDemographicCamp
    {
        return $this->demographicCamp;
    }

    public function setGroup(?Group $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }
}

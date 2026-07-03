<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedLeaderOverviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_leader_overview')]
#[ORM\Index(columns: ['m_count'])]
#[ORM\Index(columns: ['f_count'])]
#[ORM\Index(columns: ['u_count'])]
#[ORM\Index(columns: ['group_type'])]
#[ORM\Index(columns: ['data_point_date'])]
#[ORM\Entity(repositoryClass: AggregatedLeaderOverviewRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedLeaderOverview extends AggregatedEntity
{
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $mCount = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $fCount = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $uCount = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $groupType = null;

    #[ORM\OneToMany(mappedBy: 'leaderOverview', targetEntity: AggregatedLeaderOverviewLeader::class, cascade: ['persist', 'remove'])]
    private $leaders;

    /**
     * AggregatedLeaderOverview constructor.
     */
    public function __construct()
    {
        $this->leaders = new ArrayCollection();
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

    /**
     * @param mixed $uCount
     */
    public function setUCount(?int $uCount): void
    {
        $this->uCount = $uCount;
    }

    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    /**
     * @return Collection<int, AggregatedLeaderOverviewLeader>
     */
    public function getLeaders(): Collection
    {
        return $this->leaders;
    }
}

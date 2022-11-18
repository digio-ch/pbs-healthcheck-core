<?php

namespace App\Entity\Aggregated;

use App\Repository\Aggregated\AggregatedLeaderOverviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_aggregated_leader_overview", indexes={
 *     @ORM\Index(columns={"m_count"}),
 *     @ORM\Index(columns={"f_count"}),
 *     @ORM\Index(columns={"u_count"}),
 *     @ORM\Index(columns={"group_type"}),
 *     @ORM\Index(columns={"data_point_date"}),
 * })
 * @ORM\Entity(repositoryClass=AggregatedLeaderOverviewRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class AggregatedLeaderOverview extends AggregatedEntity
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $uCount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupType;

    /**
     * @ORM\OneToMany(targetEntity=AggregatedLeaderOverviewLeader::class, mappedBy="leaderOverview", cascade={"persist", "remove"})
     */
    private $leaders;

    /**
     * AggregatedLeaderOverview constructor.
     */
    public function __construct()
    {
        $this->leaders = new ArrayCollection();
    }


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
     * @return mixed
     */
    public function getUCount()
    {
        return $this->uCount;
    }

    /**
     * @param mixed $uCount
     */
    public function setUCount($uCount): void
    {
        $this->uCount = $uCount;
    }

    /**
     * @param string $groupType
     */
    public function setGroupType(string $groupType)
    {
        $this->groupType = $groupType;
    }

    /**
     * @return mixed
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * @return Collection|AggregatedLeaderOverviewLeader[]
     */
    public function getLeaders(): Collection
    {
        return $this->leaders;
    }
}

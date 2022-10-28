<?php

namespace App\Entity\Aggregated;

use App\Repository\Aggregated\AggregatedDemographicGroupRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_aggregated_demographic_group", indexes={
 *     @ORM\Index(columns={"m_count"}),
 *     @ORM\Index(columns={"f_count"}),
 *     @ORM\Index(columns={"u_count"}),
 *     @ORM\Index(columns={"m_count_leader"}),
 *     @ORM\Index(columns={"f_count_leader"}),
 *     @ORM\Index(columns={"u_count_leader"}),
 *     @ORM\Index(columns={"group_type"}),
 *     @ORM\Index(columns={"data_point_date"}),
 * })
 * @ORM\Entity(repositoryClass=AggregatedDemographicGroupRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class AggregatedDemographicGroup extends AggregatedEntity
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mCountLeader;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fCountLeader;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $uCountLeader;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupType;

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
     * @return mixed
     */
    public function getUCountLeader()
    {
        return $this->uCountLeader;
    }

    /**
     * @param mixed $uCountLeader
     */
    public function setUCountLeader($uCountLeader): void
    {
        $this->uCountLeader = $uCountLeader;
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
     * @param int $mCountLeader
     */
    public function setMCountLeader(int $mCountLeader)
    {
        $this->mCountLeader = $mCountLeader;
    }

    /**
     * @return mixed
     */
    public function getMCountLeader()
    {
        return $this->mCountLeader;
    }

    /**
     * @param int $fCountLeader
     */
    public function setFCountLeader(int $fCountLeader)
    {
        $this->fCountLeader = $fCountLeader;
    }

    /**
     * @return mixed
     */
    public function getFCountLeader()
    {
        return $this->fCountLeader;
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
}

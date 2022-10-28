<?php

namespace App\Entity\aggregated;

use App\Entity\midata\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_aggregated_demographic_camp_group", indexes={
 *     @ORM\Index(columns={"m_count"}),
 *     @ORM\Index(columns={"f_count"}),
 *     @ORM\Index(columns={"u_count"}),
 *     @ORM\Index(columns={"f_count_leader"}),
 *     @ORM\Index(columns={"m_count_leader"}),
 *     @ORM\Index(columns={"u_count_leader"}),
 *     @ORM\Index(columns={"group_type"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\DemographicCampGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AggregatedDemographicCampGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AggregatedDemographicCamp::class, inversedBy="demographicCampGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $demographicCamp;

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
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @param int $uCount
     */
    public function setUCount(int $uCount): void
    {
        $this->uCount = $uCount;
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
     * @return int
     */
    public function getUCountLeader()
    {
        return $this->uCountLeader;
    }

    /**
     * @param int $uCountLeader
     */
    public function setUCountLeader(int $uCountLeader): void
    {
        $this->uCountLeader = $uCountLeader;
    }

    /**
     * @return string
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * @param string $groupType
     */
    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    /**
     * @param AggregatedDemographicCamp $demographicCamp
     */
    public function setDemographicCamp(AggregatedDemographicCamp $demographicCamp)
    {
        $this->demographicCamp = $demographicCamp;
    }

    /**
     * @return mixed
     */
    public function getDemographicCamp()
    {
        return $this->demographicCamp;
    }

    /**
     * @param Group|null $group
     */
    public function setGroup(?Group $group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }
}

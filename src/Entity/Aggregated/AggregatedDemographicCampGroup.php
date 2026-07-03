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

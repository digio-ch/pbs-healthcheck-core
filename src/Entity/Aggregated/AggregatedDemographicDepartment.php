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

    /**
     * @param int $birthyear
     */
    public function setBirthyear(int $birthyear)
    {
        $this->birthyear = $birthyear;
    }

    /**
     * @return mixed
     */
    public function getBirthyear()
    {
        return $this->birthyear;
    }
}

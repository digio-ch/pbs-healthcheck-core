<?php

namespace App\Entity\aggregated;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_aggregated_leader_overview_leader", indexes={
 *     @ORM\Index(columns={"gender"}),
 *     @ORM\Index(columns={"name"}),
 *     @ORM\Index(columns={"birthday"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\LeaderOverviewLeaderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AggregatedLeaderOverviewLeader
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AggregatedLeaderOverview::class, inversedBy="leaders", cascade={"persist"}))
     * @ORM\JoinColumn(name="widget_leader_overview_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $leaderOverview;

    /**
     * @ORM\OneToMany(targetEntity=AggregatedLeaderOverviewQualification::class, mappedBy="leaderOverviewLeader", cascade={"persist", "remove"})
     */
    private $qualifications;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $birthday;

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
     * @param AggregatedLeaderOverview $leaderOverview
     */
    public function setLeaderOverview(AggregatedLeaderOverview $leaderOverview)
    {
        $this->leaderOverview = $leaderOverview;
    }

    /**
     * @return mixed
     */
    public function getLeaderOverview()
    {
        return $this->leaderOverview;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
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
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return Collection|AggregatedLeaderOverviewQualification[]
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }
}

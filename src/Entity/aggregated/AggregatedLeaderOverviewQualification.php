<?php

namespace App\Entity\aggregated;

use App\Entity\midata\QualificationType;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_aggregated_leader_overview_qualification", indexes={
 *     @ORM\Index(columns={"state"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\LeaderOverviewQualificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AggregatedLeaderOverviewQualification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AggregatedLeaderOverviewLeader::class, inversedBy="qualifications"))
     * @ORM\JoinColumn(name="leader_overview_leader_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $leaderOverviewLeader;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eventOrigin;

    /**
     * @ORM\ManyToOne(targetEntity=QualificationType::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $qualificationType;

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

    public function setLeaderOverviewLeader(AggregatedLeaderOverviewLeader $leaderOverviewLeader)
    {
        $this->leaderOverviewLeader = $leaderOverviewLeader;
    }

    public function getLeaderOverviewLeader()
    {
        return $this->leaderOverviewLeader;
    }

    public function setState(string $state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    /**
     * @return mixed
     */
    public function getEventOrigin()
    {
        return $this->eventOrigin;
    }

    /**
     * @param mixed $eventOrigin
     */
    public function setEventOrigin($eventOrigin): void
    {
        $this->eventOrigin = $eventOrigin;
    }

    /**
     * @return mixed|DateTimeImmutable
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $expiresAt
     */
    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return mixed
     */
    public function getQualificationType()
    {
        return $this->qualificationType;
    }

    /**
     * @param mixed $qualificationType
     */
    public function setQualificationType($qualificationType): void
    {
        $this->qualificationType = $qualificationType;
    }
}

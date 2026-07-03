<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Entity\Midata\QualificationType;
use App\Repository\Aggregated\AggregatedLeaderOverviewQualificationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_leader_overview_qualification')]
#[ORM\Index(columns: ['state'])]
#[ORM\Entity(repositoryClass: AggregatedLeaderOverviewQualificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedLeaderOverviewQualification
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'leader_overview_leader_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: AggregatedLeaderOverviewLeader::class, inversedBy: 'qualifications')]
    private ?AggregatedLeaderOverviewLeader $leaderOverviewLeader = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private $expiresAt;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $eventOrigin = null;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: QualificationType::class)]
    private ?QualificationType $qualificationType = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLeaderOverviewLeader(AggregatedLeaderOverviewLeader $leaderOverviewLeader): void
    {
        $this->leaderOverviewLeader = $leaderOverviewLeader;
    }

    public function getLeaderOverviewLeader(): ?AggregatedLeaderOverviewLeader
    {
        return $this->leaderOverviewLeader;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getEventOrigin(): ?string
    {
        return $this->eventOrigin;
    }

    /**
     * @param mixed $eventOrigin
     */
    public function setEventOrigin(?string $eventOrigin): void
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

    public function getQualificationType(): ?QualificationType
    {
        return $this->qualificationType;
    }

    /**
     * @param mixed $qualificationType
     */
    public function setQualificationType(?QualificationType $qualificationType): void
    {
        $this->qualificationType = $qualificationType;
    }
}

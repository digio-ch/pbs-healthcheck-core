<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedDemographicEnteredLeftRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_demographic_entered_left')]
#[ORM\Index(columns: ['new_count_m'])]
#[ORM\Index(columns: ['new_count_leader_m'])]
#[ORM\Index(columns: ['exit_count_m'])]
#[ORM\Index(columns: ['exit_count_leader_m'])]
#[ORM\Index(columns: ['new_count_f'])]
#[ORM\Index(columns: ['new_count_leader_f'])]
#[ORM\Index(columns: ['exit_count_leader_f'])]
#[ORM\Index(columns: ['exit_count_f'])]
#[ORM\Index(columns: ['new_count_u'])]
#[ORM\Index(columns: ['new_count_leader_u'])]
#[ORM\Index(columns: ['exit_count_u'])]
#[ORM\Index(columns: ['exit_count_leader_u'])]
#[ORM\Index(columns: ['group_type'])]
#[ORM\Index(columns: ['data_point_date'])]
#[ORM\Entity(repositoryClass: AggregatedDemographicEnteredLeftRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedDemographicEnteredLeft extends AggregatedEntity
{
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $newCountM = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $newCountLeaderM = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $exitCountM = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $exitCountLeaderM = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $newCountF = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $newCountLeaderF = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $exitCountLeaderF = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $exitCountF = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $newCountU = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $newCountLeaderU = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $exitCountU = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $exitCountLeaderU = 0;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $groupType = null;

    public function setNewCountM(int $newCountM): void
    {
        $this->newCountM = $newCountM;
    }

    public function getNewCountM(): ?int
    {
        return $this->newCountM;
    }

    public function getNewCountLeaderM(): ?int
    {
        return $this->newCountLeaderM;
    }

    public function setNewCountLeaderM(int $newCountLeaderM): void
    {
        $this->newCountLeaderM = $newCountLeaderM;
    }

    public function getExitCountM(): ?int
    {
        return $this->exitCountM;
    }

    public function setExitCountM(int $exitCountM): void
    {
        $this->exitCountM = $exitCountM;
    }

    public function setExitCountLeaderM(int $exitCountLeaderM): void
    {
        $this->exitCountLeaderM = $exitCountLeaderM;
    }

    public function getExitCountLeaderM(): ?int
    {
        return $this->exitCountLeaderM;
    }

    public function setNewCountF(int $newCountF): void
    {
        $this->newCountF = $newCountF;
    }

    public function getNewCountF(): ?int
    {
        return $this->newCountF;
    }

    public function setNewCountLeaderF(int $newCountLeaderF): void
    {
        $this->newCountLeaderF = $newCountLeaderF;
    }

    public function getNewCountLeaderF(): ?int
    {
        return $this->newCountLeaderF;
    }

    public function setExitCountLeaderF(int $exitCountLeaderF): void
    {
        $this->exitCountLeaderF = $exitCountLeaderF;
    }

    public function getExitCountLeaderF(): ?int
    {
        return $this->exitCountLeaderF;
    }

    public function setExitCountF(int $exitCountF): void
    {
        $this->exitCountF = $exitCountF;
    }

    public function getExitCountF(): ?int
    {
        return $this->exitCountF;
    }

    public function setNewCountU(int $newCountU): void
    {
        $this->newCountU = $newCountU;
    }

    public function getNewCountU(): ?int
    {
        return $this->newCountU;
    }

    public function setNewCountLeaderU(int $newCountLeaderU): void
    {
        $this->newCountLeaderU = $newCountLeaderU;
    }

    public function getNewCountLeaderU(): ?int
    {
        return $this->newCountLeaderU;
    }

    public function setExitCountU(int $exitCountU): void
    {
        $this->exitCountU = $exitCountU;
    }

    public function getExitCountU(): ?int
    {
        return $this->exitCountU;
    }

    public function setExitCountLeaderU(int $exitCountLeaderU): void
    {
        $this->exitCountLeaderU = $exitCountLeaderU;
    }

    public function getExitCountLeaderU(): ?int
    {
        return $this->exitCountLeaderU;
    }

    public function setGroupType(string $groupType): void
    {
        $this->groupType = $groupType;
    }

    public function getGroupType(): ?string
    {
        return $this->groupType;
    }
}

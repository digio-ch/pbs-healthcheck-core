<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedLeaderOverviewLeaderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_leader_overview_leader')]
#[ORM\Index(columns: ['gender'])]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['birthday'])]
#[ORM\Entity(repositoryClass: AggregatedLeaderOverviewLeaderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedLeaderOverviewLeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\JoinColumn(name: 'widget_leader_overview_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: AggregatedLeaderOverview::class, cascade: ['persist'], inversedBy: 'leaders')]
    private ?AggregatedLeaderOverview $leaderOverview = null;

    #[ORM\OneToMany(mappedBy: 'leaderOverviewLeader', targetEntity: AggregatedLeaderOverviewQualification::class, cascade: ['persist', 'remove'])]
    private $qualifications;

    #[ORM\Column(type: Types::STRING, length: 1, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private $birthday;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLeaderOverview(AggregatedLeaderOverview $leaderOverview): void
    {
        $this->leaderOverview = $leaderOverview;
    }

    public function getLeaderOverview(): ?AggregatedLeaderOverview
    {
        return $this->leaderOverview;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
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
     * @return Collection<int, AggregatedLeaderOverviewQualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }
}

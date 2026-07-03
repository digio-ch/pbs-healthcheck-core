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
     * @return Collection<int, AggregatedLeaderOverviewQualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }
}

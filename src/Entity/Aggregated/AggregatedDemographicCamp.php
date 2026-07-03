<?php

namespace App\Entity\Aggregated;

use Doctrine\DBAL\Types\Types;
use App\Repository\Aggregated\AggregatedDemographicCampRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'hc_aggregated_demographic_camp')]
#[ORM\Index(columns: ['data_point_date'], name: 'data_point_date_idx')]
#[ORM\Index(columns: ['start_date'], name: 'start_date_idx')]
#[ORM\Entity(repositoryClass: AggregatedDemographicCampRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AggregatedDemographicCamp extends AggregatedEntity
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected $startDate;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected ?string $campName = null;

    #[ORM\OneToMany(mappedBy: 'demographicCamp', targetEntity: AggregatedDemographicCampGroup::class, cascade: ['remove'])]
    protected $demographicCampGroups;

    public function __construct()
    {
        $this->demographicCampGroups = new ArrayCollection();
    }

    public function setCampName(?string $campName)
    {
        $this->campName = $campName;
    }

    public function getCampName()
    {
        return $this->campName;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * @param DateTimeImmutable|null $startDate
     */
    public function setStartDate(?DateTimeImmutable $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return Collection<int, AggregatedDemographicCampGroup>
     */
    public function getDemographicCampGroups(): Collection
    {
        return $this->demographicCampGroups;
    }

    /**
     * @param AggregatedDemographicCampGroup $demographicCampGroup
     */
    public function addDemographicCampGroup(AggregatedDemographicCampGroup $demographicCampGroup)
    {
        if (!$this->hasDemographicCampGroup($demographicCampGroup)) {
            $demographicCampGroup->setDemographicCamp($this);
            $this->demographicCampGroups->add($demographicCampGroup);
        }
    }

    public function removeDemographicCampGroup(AggregatedDemographicCampGroup $demographicCampGroup)
    {
        if ($this->hasDemographicCampGroup($demographicCampGroup)) {
            $this->demographicCampGroups->removeElement($demographicCampGroup);
            $demographicCampGroup->setDemographicCamp(null);
        }
    }

    public function hasDemographicCampGroup(AggregatedDemographicCampGroup $demographicCampGroup)
    {
        return $this->demographicCampGroups->contains($demographicCampGroup);
    }
}

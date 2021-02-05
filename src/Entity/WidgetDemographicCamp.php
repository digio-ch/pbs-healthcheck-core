<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="hc_widget_demographic_camp", indexes={@ORM\Index(name="data_point_date_idx", columns={"data_point_date"}), @ORM\Index(name="start_date_idx", columns={"start_date"})})
 * @ORM\Entity(repositoryClass="App\Repository\WidgetDemographicCampRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WidgetDemographicCamp extends Widget
{
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $startDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $campName;

    /**
     * @ORM\OneToMany(targetEntity="DemographicCampGroup", mappedBy="demographicCamp", cascade={"remove"})
     */
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

    public function getDemographicCampGroups(): Collection
    {
        return $this->demographicCampGroups;
    }

    /**
     * @param DemographicCampGroup $demographicCampGroup
     */
    public function addDemographicCampGroup(DemographicCampGroup $demographicCampGroup)
    {
        if (!$this->hasDemographicCampGroup($demographicCampGroup)) {
            $demographicCampGroup->setDemographicCamp($this);
            $this->demographicCampGroups->add($demographicCampGroup);
        }
    }

    public function removeDemographicCampGroup(DemographicCampGroup $demographicCampGroup)
    {
        if ($this->hasDemographicCampGroup($demographicCampGroup)) {
            $this->demographicCampGroups->removeElement($demographicCampGroup);
            $demographicCampGroup->setDemographicCamp(null);
        }
    }

    public function hasDemographicCampGroup(DemographicCampGroup $demographicCampGroup)
    {
        return $this->demographicCampGroups->contains($demographicCampGroup);
    }
}

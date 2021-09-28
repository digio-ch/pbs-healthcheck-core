<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="midata_event", indexes={
 *     @ORM\Index(columns={"name"})
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"midata_id", "sync_group_id"})
 * })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"course" = "Course", "camp" = "Camp"})
 */
abstract class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $midataId;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="sync_group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $syncGroup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name = '';

    /**
     * @ORM\OneToMany(targetEntity="EventGroup", mappedBy="event", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="PersonEvent", mappedBy="event", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EventDate", mappedBy="event", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $eventDates;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->eventDates = new ArrayCollection();
        $this->persons = new ArrayCollection();
    }

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
     * @return mixed
     */
    public function getMidataId()
    {
        return $this->midataId;
    }

    /**
     * @param int $midataId
     */
    public function setMidataId(int $midataId)
    {
        $this->midataId = $midataId;
    }

    public function getSyncGroup(): Group
    {
        return $this->syncGroup;
    }

    public function setSyncGroup($syncGroup)
    {
        $this->syncGroup = $syncGroup;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @param EventGroup $group
     */
    public function addGroup(EventGroup $group)
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }
    }

    /**
     * @param EventGroup $group
     */
    public function removeGroup(EventGroup $group)
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }
    }

    /**
     * @return Collection
     */
    public function getEventDates(): Collection
    {
        return $this->eventDates;
    }

    /**
     * @param Collection $eventDates
     */
    public function setEventDates(Collection $eventDates): void
    {
        $this->eventDates = $eventDates;
    }

    public function addEventDate(EventDate $eventDate): self {
        if (!$this->eventDates->contains($eventDate)) {
            $this->eventDates[] = $eventDate;
            $eventDate->setEvent($this);
        }

        return $this;
    }

    public function clearEventDates(): self {
        $this->eventDates = new ArrayCollection();

        return $this;
    }

    public function addPerson(PersonEvent $personEvent): self {
        if (!$this->persons->contains($personEvent)) {
            $this->persons[] = $personEvent;
            $personEvent->setEvent($this);
        }

        return $this;
    }

    public function clearPersons(): self {
        $this->persons = new ArrayCollection();

        return $this;
    }
}

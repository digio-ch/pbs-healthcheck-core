<?php

namespace App\Entity\Midata;

use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_event')]
#[ORM\Index(columns: ['name'])]
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['course' => 'Course', 'camp' => 'Camp'])]
abstract class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = '';

    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventGroup::class, cascade: ['persist', 'remove'])]
    private $groups;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: PersonEvent::class, cascade: ['persist', 'remove'])]
    private $persons;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventDate::class)]
    private $eventDates;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->eventDates = new ArrayCollection();
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
}

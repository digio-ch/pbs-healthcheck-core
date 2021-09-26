<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="midata_person_event")
 * @ORM\Entity(repositoryClass="App\Repository\PersonEventRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PersonEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="persons")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
     */
    private $person;

    /***
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $qualified;

    /**
     * @ORM\ManyToMany(targetEntity="PersonEventType")
     * @ORM\JoinTable(name="midata_person_event_person_event_type",
     *      joinColumns={@ORM\JoinColumn(name="person_event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="person_event_type_id", referencedColumnName="id")}
     *      )
     */
    private $personEventTypes;

    /**
     * PersonEvent constructor.
     */
    public function __construct()
    {
        $this->personEventTypes = new ArrayCollection();
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
     * @param Person|null $person
     */
    public function setPerson(?Person $person)
    {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Event|null $event
     */
    public function setEvent(?Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param null|string $qualified
     */
    public function setQualified(?string $qualified)
    {
        $this->qualified = $qualified;
    }

    /**
     * @return mixed
     */
    public function getQualified()
    {
        return $this->qualified;
    }

    /**
     * @return ArrayCollection
     */
    public function getPersonEventTypes(): ArrayCollection
    {
        return $this->personEventTypes;
    }

    /**
     * @param PersonEventType $personEventType
     */
    public function addPersonEventType(PersonEventType $personEventType)
    {
        if (!$this->personEventTypes->contains($personEventType)) {
            $this->personEventTypes[] = $personEventType;
        }
    }

    /**
     * @param PersonEventType $personEventType
     */
    public function removePersonEventType(PersonEventType $personEventType)
    {
        if ($this->personEventTypes->contains($personEventType)) {
            $this->personEventTypes->removeElement($personEventType);
        }
    }
}

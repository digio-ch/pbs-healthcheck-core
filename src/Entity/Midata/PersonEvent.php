<?php

namespace App\Entity\Midata;

use App\Repository\Midata\PersonEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'midata_person_event')]
#[ORM\Entity(repositoryClass: PersonEventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PersonEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'persons')]
    private $event;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'events')]
    private $person;

    /***
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $qualified;

    #[ORM\JoinTable(name: 'midata_person_event_person_event_type')]
    #[ORM\JoinColumn(name: 'person_event_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'person_event_type_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: PersonEventType::class)]
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
